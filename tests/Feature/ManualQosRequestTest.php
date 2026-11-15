<?php

namespace Tests\Feature;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ManualQosRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_manual_test_queues_request_for_agent_and_clears_after_measurement(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now());

        $this->actingAs($user)
            ->post(route('qos.test', $school))
            ->assertRedirect(route('dashboard', ['school_id' => $school->id]))
            ->assertSessionHas('success');

        $school->refresh();

        $this->assertNotNull($school->measure_requested_at);

        $this->withHeader('X-Agent-Token', $school->agent_token)
            ->getJson('/api/agent/config')
            ->assertOk()
            ->assertJsonPath('measure_now', true);

        $this->withHeader('X-Agent-Token', $school->agent_token)
            ->postJson('/api/agent/measurements', [
                'throughput' => 50,
                'delay' => 10,
                'jitter' => 2,
                'packet_loss' => 0,
                'ping' => 10,
                'success' => true,
            ])
            ->assertCreated();

        $school->refresh();

        $this->assertNull($school->measure_requested_at);
    }

    public function test_agent_measurement_uses_measured_at_from_payload(): void
    {
        [$school] = $this->makeSchoolWithUser(now());
        $measuredAt = now()->subMinutes(12)->startOfSecond();

        $this->withHeader('X-Agent-Token', $school->agent_token)
            ->postJson('/api/agent/measurements', [
                'throughput' => 50,
                'delay' => 10,
                'jitter' => 2,
                'packet_loss' => 0,
                'ping' => 10,
                'success' => true,
                'measured_at' => $measuredAt->toIso8601String(),
            ])
            ->assertCreated();

        $measurement = QosMeasurement::where('school_id', $school->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($measurement);
        $this->assertSame(
            $measuredAt->format('Y-m-d H:i:s'),
            $measurement->measured_at->format('Y-m-d H:i:s')
        );
    }

    public function test_manual_test_is_rejected_when_requested_again_within_five_minutes(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now());

        $school->forceFill([
            'last_manual_test_at' => now()->subMinute(),
        ])->save();

        $this->actingAs($user)
            ->post(route('qos.test', $school))
            ->assertRedirect(route('dashboard', ['school_id' => $school->id]))
            ->assertSessionHas('error');

        $school->refresh();

        $this->assertNull($school->measure_requested_at);
    }

    public function test_dashboard_uses_latest_valid_measurement_instead_of_failed_attempt(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now());

        QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => 50,
            'delay' => 10,
            'jitter' => 2,
            'packet_loss' => 0,
            'ping' => 10,
            'success' => true,
            'measured_at' => now()->subMinutes(10),
        ]);

        QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => null,
            'delay' => 2,
            'jitter' => 1.67,
            'packet_loss' => 0,
            'ping' => 2,
            'success' => false,
            'measured_at' => now()->subMinute(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['school_id' => $school->id]))
            ->assertOk()
            ->assertSee('50.00')
            ->assertDontSee('BELUM ADA');
    }

    public function test_manual_test_is_rejected_when_agent_is_offline(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now()->subMinutes(20));

        $this->actingAs($user)
            ->post(route('qos.test', $school))
            ->assertRedirect(route('dashboard', ['school_id' => $school->id]))
            ->assertSessionHas('error');

        $school->refresh();

        $this->assertNull($school->measure_requested_at);
    }

    public function test_manual_test_is_rejected_when_agent_never_connected(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(null);

        $this->actingAs($user)
            ->post(route('qos.test', $school))
            ->assertRedirect(route('dashboard', ['school_id' => $school->id]))
            ->assertSessionHas('error');

        $school->refresh();

        $this->assertNull($school->measure_requested_at);
    }

    public function test_manual_test_is_rejected_during_speedtest_cooldown(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now());

        $school->forceFill([
            'agent_speedtest_cooldown_until' => now()->addMinutes(30),
            'agent_speedtest_notice' => 'Speedtest dibatasi oleh Ookla (Limit reached).',
        ])->save();

        $this->actingAs($user)
            ->post(route('qos.test', $school))
            ->assertRedirect(route('dashboard', ['school_id' => $school->id]))
            ->assertSessionHas('error');

        $school->refresh();

        $this->assertNull($school->measure_requested_at);
    }

    public function test_failed_measurement_is_stored_without_clearing_cooldown(): void
    {
        [$school] = $this->makeSchoolWithUser(now());

        $school->forceFill([
            'agent_speedtest_cooldown_until' => now()->addMinutes(10),
            'agent_speedtest_notice' => 'Speedtest dibatasi oleh Ookla (Limit reached).',
        ])->save();

        $this->withHeader('X-Agent-Token', $school->agent_token)
            ->postJson('/api/agent/measurements', [
                'throughput' => null,
                'delay' => 2,
                'jitter' => 1.67,
                'packet_loss' => 0,
                'ping' => 2,
                'success' => false,
                'raw_output' => 'Limit reached',
            ])
            ->assertCreated();

        $school->refresh();

        $this->assertNotNull($school->agent_speedtest_cooldown_until);
        $this->assertSame(
            'Speedtest dibatasi oleh Ookla (Limit reached).',
            $school->agent_speedtest_notice
        );

        $measurement = QosMeasurement::where('school_id', $school->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($measurement);
        $this->assertFalse($measurement->success);
        $this->assertNull($measurement->throughput);
    }

    public function test_dashboard_shows_paused_auto_notice_after_cooldown(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now());

        $school->forceFill([
            'agent_speedtest_cooldown_until' => now()->subMinute(),
            'agent_speedtest_notice' => 'Auto speedtest dijeda setelah limit. Tekan Tes QoS untuk mencoba lagi.',
        ])->save();

        $this->actingAs($user)
            ->get(route('dashboard', ['school_id' => $school->id]))
            ->assertOk()
            ->assertSee('Auto speedtest dijeda setelah limit.');
    }

    public function test_dashboard_shows_speedtest_cooldown_notice(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(now());

        $school->forceFill([
            'agent_speedtest_cooldown_until' => now()->addMinutes(30),
            'agent_speedtest_notice' => 'Speedtest dibatasi oleh Ookla (Limit reached).',
        ])->save();

        $this->actingAs($user)
            ->get(route('dashboard', ['school_id' => $school->id]))
            ->assertOk()
            ->assertSee('Speedtest dibatasi oleh Ookla')
            ->assertSee('Speedtest Cooldown');
    }

    private function makeSchoolWithUser($lastSeenAt): array
    {
        $school = School::create([
            'nama_sekolah' => 'TEMP Manual Test ' . Str::random(5),
            'ip_target' => '192.168.1.1',
            'bandwidth' => 100,
            'paket_isp' => null,
            'status' => true,
            'agent_last_seen_at' => $lastSeenAt,
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'role' => 'user',
            'name' => 'Manual Test ' . Str::random(5),
            'email' => 'manual-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);

        return [$school, $user];
    }
}
