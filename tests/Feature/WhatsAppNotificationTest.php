<?php

namespace Tests\Feature;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
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

    public function test_notification_is_sent_when_condition_changes_to_bad(): void
    {
        [$school] = $this->makeSchoolWithUser('08123456789', true);

        $this->makeMeasurement($school, [
            'throughput' => 90,
            'delay' => 50,
            'jitter' => 10,
            'packet_loss' => 0,
            'ping' => 10,
        ]);

        Http::assertNothingSent();

        $this->makeMeasurement($school, [
            'throughput' => 10,
            'delay' => 500,
            'jitter' => 200,
            'packet_loss' => 20,
            'ping' => 500,
        ]);

        Http::assertSentCount(1);

        Http::assertSent(function ($request) use ($school) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '628123456789'
                && str_contains($request['message'], $school->nama_sekolah)
                && str_contains($request['message'], 'BURUK');
        });

        $this->assertDatabaseHas('whatsapp_notifications', [
            'school_id' => $school->id,
            'event' => 'condition_change',
            'status' => 'sent',
        ]);

        $this->makeMeasurement($school, [
            'throughput' => 9,
            'delay' => 520,
            'jitter' => 210,
            'packet_loss' => 22,
            'ping' => 510,
        ]);

        Http::assertSentCount(1);
    }

    public function test_notification_is_not_sent_when_disabled_or_number_missing(): void
    {
        [$school] = $this->makeSchoolWithUser(null, true);

        $this->makeMeasurement($school, [
            'throughput' => 10,
            'delay' => 500,
            'jitter' => 200,
            'packet_loss' => 20,
            'ping' => 500,
        ]);

        [$disabledSchool] = $this->makeSchoolWithUser('081298765432', false);

        $this->makeMeasurement($disabledSchool, [
            'throughput' => 10,
            'delay' => 500,
            'jitter' => 200,
            'packet_loss' => 20,
            'ping' => 500,
        ]);

        Http::assertNothingSent();
        $this->assertDatabaseCount('whatsapp_notifications', 0);
    }

    public function test_notification_is_sent_only_when_measurement_starts_failing(): void
    {
        [$school] = $this->makeSchoolWithUser('08123456789', true);

        $this->makeMeasurement($school, [
            'throughput' => 90,
            'delay' => 50,
            'jitter' => 10,
            'packet_loss' => 0,
            'ping' => 10,
        ]);

        $this->makeMeasurement($school, [], false);

        Http::assertSentCount(1);

        $this->assertDatabaseHas('whatsapp_notifications', [
            'school_id' => $school->id,
            'event' => 'failure',
            'status' => 'sent',
        ]);

        $this->makeMeasurement($school, [], false);

        Http::assertSentCount(1);
    }

    public function test_settings_can_store_number_and_send_test_message(): void
    {
        [$school, $user] = $this->makeSchoolWithUser(null, false);

        $this->actingAs($user)
            ->put(route('settings.whatsapp.update'), [
                'whatsapp_number' => '0812-3456-7890',
                'whatsapp_notifications' => '1',
            ])
            ->assertRedirect(route('settings.index'));

        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
            'key' => 'whatsapp_number',
            'value' => '6281234567890',
        ]);

        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
            'key' => 'whatsapp_notifications',
            'value' => '1',
        ]);

        $this->actingAs($user)
            ->post(route('settings.whatsapp.test'))
            ->assertRedirect(route('settings.index'));

        Http::assertSentCount(1);

        $this->assertDatabaseHas('whatsapp_notifications', [
            'school_id' => $school->id,
            'event' => 'test',
            'status' => 'sent',
        ]);
    }

    private function makeSchoolWithUser(
        ?string $phone,
        bool $enabled
    ): array {
        $school = School::create([
            'nama_sekolah' => 'TEMP WA Test ' . Str::random(5),
            'ip_target' => '192.168.1.1',
            'bandwidth' => 100,
            'paket_isp' => null,
            'status' => true,
        ]);

        $user = User::create([
            'school_id' => $school->id,
            'role' => 'user',
            'name' => 'WA Test ' . Str::random(5),
            'email' => 'wa-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);

        if ($phone !== null) {
            UserSetting::setForUser($user->id, 'whatsapp_number', $phone);
        }

        UserSetting::setForUser(
            $user->id,
            'whatsapp_notifications',
            $enabled ? '1' : '0'
        );

        return [$school, $user];
    }

    private function makeMeasurement(
        School $school,
        array $values,
        bool $success = true
    ): QosMeasurement {
        return QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => $values['throughput'] ?? null,
            'delay' => $values['delay'] ?? null,
            'jitter' => $values['jitter'] ?? null,
            'packet_loss' => $values['packet_loss'] ?? null,
            'ping' => $values['ping'] ?? null,
            'success' => $success,
            'raw_output' => null,
            'measured_at' => now(),
        ]);
    }
}
