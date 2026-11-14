<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;

class QosTestController extends Controller
{
    public function store(School $school): RedirectResponse
    {
        $user = auth()->user();

        if (
            ! $user ||
            (int) $user->school_id !== (int) $school->id
        ) {
            abort(403, 'Anda hanya dapat melakukan tes untuk sekolah sendiri.');
        }

        if (blank($school->ip_target)) {
            return $this->redirectWithStatus(
                $school,
                'error',
                'IP target belum tersedia. Pasang agent sekolah agar gateway terdeteksi otomatis.'
            );
        }

        if (! $school->agent_last_seen_at) {
            return $this->redirectWithStatus(
                $school,
                'error',
                'Agent sekolah belum pernah terhubung. Instal agent terlebih dahulu.'
            );
        }

        if (! $school->agentIsOnline()) {
            return $this->redirectWithStatus(
                $school,
                'error',
                'Agent sekolah sedang offline. Permintaan tes tidak dikirim.'
            );
        }

        if (
            $school->agent_speedtest_cooldown_until !== null &&
            $school->agent_speedtest_cooldown_until->isFuture()
        ) {
            return $this->redirectWithStatus(
                $school,
                'error',
                'Speedtest sedang dibatasi oleh Ookla sampai '
                    . $school->agent_speedtest_cooldown_until->format('d/m/Y H:i')
                    . '. Coba lagi setelah waktu tersebut.'
            );
        }

        if (
            $school->measure_requested_at !== null &&
            $school->measure_requested_at->gt(now()->subMinutes(10))
        ) {
            return $this->redirectWithStatus(
                $school,
                'success',
                'Permintaan Tes QoS sudah dikirim dan sedang menunggu agent.'
            );
        }

        $school->forceFill([
            'measure_requested_at' => now(),
        ])->save();

        return $this->redirectWithStatus(
            $school,
            'success',
            'Permintaan Tes QoS dikirim ke agent. Hasil akan muncul setelah agent menyelesaikan pengukuran.'
        );
    }

    private function redirectWithStatus(
        School $school,
        string $type,
        string $message
    ): RedirectResponse {
        return redirect()
            ->route('dashboard', [
                'school_id' => $school->id,
            ])
            ->with($type, $message);
    }
}
