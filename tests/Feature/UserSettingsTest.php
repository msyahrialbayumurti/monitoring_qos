<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserSettingsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_settings_are_scoped_to_each_account(): void
    {
        Setting::updateOrCreate(
            ['key' => 'monitoring_interval'],
            ['value' => '5']
        );

        Setting::updateOrCreate(
            ['key' => 'dashboard_refresh_interval'],
            ['value' => '30']
        );

        $schoolA = $this->makeSchool('TEMP Settings A');
        $schoolB = $this->makeSchool('TEMP Settings B');

        $userA = $this->makeUser($schoolA->id);
        $userB = $this->makeUser($schoolB->id);

        $this->actingAs($userA)
            ->put(route('settings.update'), [
                'monitoring_interval' => 15,
                'dashboard_refresh_interval' => 120,
            ])
            ->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('user_settings', [
            'user_id' => $userA->id,
            'key' => 'monitoring_interval',
            'value' => '15',
        ]);

        $this->assertDatabaseHas('user_settings', [
            'user_id' => $userA->id,
            'key' => 'dashboard_refresh_interval',
            'value' => '120',
        ]);

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $userB->id,
        ]);

        $schoolA->forceFill(['agent_token' => 'settings-token-a'])->save();
        $schoolB->forceFill(['agent_token' => 'settings-token-b'])->save();

        $this->withHeader('X-Agent-Token', 'settings-token-a')
            ->getJson('/api/agent/config')
            ->assertOk()
            ->assertJsonPath('monitoring_interval_minutes', 15);

        $this->withHeader('X-Agent-Token', 'settings-token-b')
            ->getJson('/api/agent/config')
            ->assertOk()
            ->assertJsonPath('monitoring_interval_minutes', 5);

        $this->actingAs($userB)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Akun');
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
