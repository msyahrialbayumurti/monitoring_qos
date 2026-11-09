<?php

namespace App\Http\Controllers;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AgentController extends Controller
{
    public function enroll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enrollment_token' => ['required', 'string', 'min:16', 'max:64'],
            'agent_version' => ['nullable', 'string', 'max:50'],
        ]);

        $school = DB::transaction(function () use ($validated) {
            $school = School::query()
                ->where('agent_enrollment_token', $validated['enrollment_token'])
                ->lockForUpdate()
                ->first();

            if (! $school || ! $school->status) {
                return null;
            }

            $expiresAt = $school->agent_enrollment_expires_at;

            if (! $expiresAt || $expiresAt->isPast()) {
                return null;
            }

            $school->forceFill([
                'agent_token' => Str::random(48),
                'agent_enrollment_token' => null,
                'agent_enrollment_expires_at' => null,
                'agent_last_seen_at' => now(),
                'agent_version' => $validated['agent_version'] ?? $school->agent_version,
            ])->save();

            return $school;
        });

        if (! $school) {
            return response()->json([
                'message' => 'Kode enrollment tidak valid, sudah dipakai, atau kedaluwarsa.',
            ], 422);
        }

        $requestIp = $request->ip();

        $this->updatePresence($school, [
            'gateway' => null,
            'public_ip' => $requestIp && filter_var($requestIp, FILTER_VALIDATE_IP)
                ? $requestIp
                : null,
            'agent_version' => $validated['agent_version'] ?? null,
        ]);

        return response()->json([
            'message' => 'Enrollment berhasil.',
            'agent_token' => $school->agent_token,
            'school' => [
                'id' => $school->id,
                'nama_sekolah' => $school->nama_sekolah,
                'ip_target' => $school->ip_target,
            ],
            'server_time' => now()->toIso8601String(),
        ], 201);
    }

    public function config(Request $request): JsonResponse
    {
        $school = $this->school($request);

        $ownerId = User::query()
            ->where('school_id', $school->id)
            ->value('id');

        $interval = (int) UserSetting::getForUser(
            $ownerId,
            'monitoring_interval',
            '5'
        );

        if (! in_array($interval, [1, 5, 10, 15], true)) {
            $interval = 5;
        }

        $lastMeasurement = QosMeasurement::query()
            ->where('school_id', $school->id)
            ->latest('measured_at')
            ->first();

        return response()->json([
            'school' => [
                'id' => $school->id,
                'nama_sekolah' => $school->nama_sekolah,
                'ip_target' => $school->ip_target,
                'bandwidth' => (float) $school->bandwidth,
                'paket_isp' => $school->paket_isp,
            ],
            'monitoring_interval_minutes' => $interval,
            'recommended_interval_seconds' => max(60, $interval * 60),
            'measure_now' => $school->measure_requested_at !== null,
            'measure_requested_at' => $school->measure_requested_at?->toIso8601String(),
            'last_measurement_at' => $lastMeasurement?->measured_at?->toIso8601String(),
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $school = $this->school($request);

        $validated = $request->validate([
            'gateway' => ['nullable', 'ip'],
            'public_ip' => ['nullable', 'ip'],
            'agent_version' => ['nullable', 'string', 'max:50'],
            'error' => ['nullable', 'string', 'max:1000'],
        ]);

        $publicIp = $validated['public_ip'] ?? $request->ip();

        $this->updatePresence($school, [
            'gateway' => $validated['gateway'] ?? null,
            'public_ip' => $publicIp,
            'agent_version' => $validated['agent_version'] ?? null,
        ]);

        if (! empty($validated['error'])) {
            Log::warning('Agent melaporkan error', [
                'school_id' => $school->id,
                'school' => $school->nama_sekolah,
                'error' => $validated['error'],
            ]);
        }

        return response()->json([
            'message' => 'Heartbeat diterima.',
            'school_id' => $school->id,
            'agent_online' => true,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function storeMeasurement(Request $request): JsonResponse
    {
        $school = $this->school($request);

        $validated = $request->validate([
            'throughput' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'delay' => ['nullable', 'numeric', 'min:0', 'max:600000'],
            'jitter' => ['nullable', 'numeric', 'min:0', 'max:600000'],
            'packet_loss' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ping' => ['nullable', 'numeric', 'min:0', 'max:600000'],
            'success' => ['required', 'boolean'],
            'raw_output' => ['nullable', 'string', 'max:30000'],
            'gateway' => ['nullable', 'ip'],
            'public_ip' => ['nullable', 'ip'],
            'agent_version' => ['nullable', 'string', 'max:50'],
        ]);

        $publicIp = $validated['public_ip'] ?? $request->ip();

        $measurement = QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => $validated['throughput'] ?? null,
            'delay' => $validated['delay'] ?? null,
            'jitter' => $validated['jitter'] ?? null,
            'packet_loss' => $validated['packet_loss'] ?? null,
            'ping' => $validated['ping'] ?? null,
            'success' => $validated['success'],
            'raw_output' => $validated['raw_output'] ?? null,
            'measured_at' => now(),
        ]);

        if ($school->measure_requested_at !== null) {
            $school->measure_requested_at = null;
        }

        $this->updatePresence($school, [
            'gateway' => $validated['gateway'] ?? null,
            'public_ip' => $publicIp,
            'agent_version' => $validated['agent_version'] ?? null,
        ]);

        return response()->json([
            'message' => 'Pengukuran diterima.',
            'measurement_id' => $measurement->id,
            'school_id' => $school->id,
            'received_at' => $measurement->measured_at->toIso8601String(),
        ], 201);
    }

    private function school(Request $request): School
    {
        return $request->attributes->get('agent_school');
    }

    private function updatePresence(School $school, array $data): void
    {
        $gateway = $data['gateway'] ?? null;
        $publicIp = $data['public_ip'] ?? null;

        $school->agent_last_seen_at = now();

        if ($gateway) {
            $school->agent_gateway = $gateway;

            if (blank($school->ip_target)) {
                $school->ip_target = $gateway;
            }
        }

        if ($data['agent_version'] ?? null) {
            $school->agent_version = $data['agent_version'];
        }

        $publicIpChanged = $publicIp
            && $publicIp !== $school->agent_public_ip;

        if ($publicIp) {
            $school->agent_public_ip = $publicIp;
        }

        if ($publicIp && ($publicIpChanged || blank($school->agent_isp))) {
            $detectedIsp = $this->detectIsp($publicIp);

            if ($detectedIsp) {
                $school->agent_isp = $detectedIsp;
            }
        }

        $school->save();
    }

    private function detectIsp(string $ip): ?string
    {
        if (! filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        )) {
            return null;
        }

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get('http://ip-api.com/json/' . $ip, [
                    'fields' => 'status,message,isp,org,as',
                ]);

            if (! $response->ok() || $response->json('status') !== 'success') {
                return null;
            }

            return $response->json('isp')
                ?: $response->json('org')
                ?: $response->json('as');
        } catch (Throwable $e) {
            Log::warning('Gagal mendeteksi ISP agent', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
