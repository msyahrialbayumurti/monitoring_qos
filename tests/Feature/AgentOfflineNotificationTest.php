<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class AgentOfflineNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.fonnte.token', 'test-token');
        config()->set('services.fonnte.url', 'https://api.fonnte.com/send');
        config()->set('services.fonnte.timeout', 5);

        Http::fake([
            'api.fonnte.com/*' => Http::response([
                'status' => true,
                'detail' => 'sent',
            ], 200),
        ]);
    }

    public function test_offline_agent_sends_notification_once_and_recovery_clears_flag(): void
    {
        $this->markOtherSchoolsOnline();

        [$school] = $this->makeSchoolWithUser(now()->subMinutes(20));

        $this->artisan('agents:check-offline')->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_notifications', [
            'school_id' => $school->id,
            'event' => 'agent_offline',
            'status' => 'sent',
        ]);

        $school->refresh();

        $this->assertNotNull($school->agent_offline_notified_at);

        $this->artisan('agents:check-offline')->assertExitCode(0);

        $this->assertSame(
            1,
            \App\Models\WhatsAppNotification::query()
                ->where('school_id', $school->id)
                ->where('event', 'agent_offline')
                ->count()
        );

        $school->forceFill([
            'agent_last_seen_at' => now(),
        ])->save();

        $this->artisan('agents:check-offline')->assertExitCode(0);

        $this->assertDatabaseHas('whatsapp_notifications', [
            'school_id' => $school->id,
            'event' => 'agent_recovery',
            'status' => 'sent',
        ]);

        $school->refresh();

        $this->assertNull($school->agent_offline_notified_at);
    }

    private function markOtherSchoolsOnline(): void
    {
        School::query()->update([
            'agent_last_seen_at' => now(),
            'agent_offline_notified_at' => null,
        ]);
    }

    private function makeSchoolWithUser($lastSeenAt): array
    {
        $school = School::create([
            'nama_sekolah' => 'TEMP Offline Test ' . Str::random(5),
            'ip_target' => '192.168.1.1',
            'bandwidth' => 100,
            'paket_isp' => null,
            'status' => true,
            'agent_last_seen_at' => $lastSeenAt,
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'role' => 'user',
            'name' => 'Offline Test ' . Str::random(5),
            'email' => 'offline-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);

        UserSetting::setForUser($user->id, 'whatsapp_number', '08123456789');
        UserSetting::setForUser($user->id, 'whatsapp_notifications', '1');

        return [$school, $user];
    }
}
