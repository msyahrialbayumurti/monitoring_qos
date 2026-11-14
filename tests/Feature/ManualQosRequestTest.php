<?php

namespace Tests\Feature;

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
