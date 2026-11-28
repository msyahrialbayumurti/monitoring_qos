<?php

namespace Tests\Feature;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_agent_interval_is_fixed_and_settings_page_is_scoped(): void
    {
        $schoolA = $this->makeSchool('TEMP Settings A');
        $schoolB = $this->makeSchool('TEMP Settings B');

        $userA = $this->makeUser($schoolA->id);
        $userB = $this->makeUser($schoolB->id);

        $schoolA->forceFill(['agent_token' => 'settings-token-a'])->save();
        $schoolB->forceFill(['agent_token' => 'settings-token-b'])->save();

        $this->withHeader('X-Agent-Token', 'settings-token-a')
            ->getJson('/api/agent/config')
            ->assertOk()
            ->assertJsonPath('monitoring_interval_minutes', 5);

        $this->withHeader('X-Agent-Token', 'settings-token-b')
            ->getJson('/api/agent/config')
            ->assertOk()
            ->assertJsonPath('monitoring_interval_minutes', 5);

        $this->actingAs($userA)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan NETMONITOR')
            ->assertDontSee('Interval Monitoring QoS');

        $this->actingAs($userB)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Akun');
    }

    public function test_dashboard_latest_endpoint_returns_latest_measurement_id(): void
    {
        $school = $this->makeSchool('TEMP Dashboard Latest');
        $user = $this->makeUser($school->id);

        $measurement = QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => 50,
            'delay' => 20,
            'jitter' => 2,
            'packet_loss' => 0,
            'ping' => 10,
            'success' => true,
            'measured_at' => now(),
        ]);

        $this->actingAs($user)
            ->getJson(route('dashboard.latest'))
            ->assertOk()
            ->assertJsonPath('measurement_id', $measurement->id);
    }

    private function makeSchool(string $name): School
    {
        return School::create([
            'nama_sekolah' => $name,
            'ip_target' => null,
            'bandwidth' => null,
            'paket_isp' => null,
            'status' => true,
        ]);
    }

    private function makeUser(int $schoolId): User
    {
        return User::create([
            'school_id' => $schoolId,
            'role' => 'user',
            'name' => 'Settings Test ' . Str::random(5),
            'email' => 'settings-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);
    }
}
