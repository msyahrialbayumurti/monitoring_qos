<?php

namespace Tests\Feature;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class MeasurementDeletionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_delete_selected_history_and_all_filtered_data(): void
    {
        $admin = $this->makeUser('admin');
        $schoolA = $this->makeSchool('TEMP Delete A');
        $schoolB = $this->makeSchool('TEMP Delete B');

        $first = $this->makeMeasurement($schoolA);
        $second = $this->makeMeasurement($schoolA);
        $other = $this->makeMeasurement($schoolB);

        $this->actingAs($admin)
            ->get(route('monitoring.history'))
            ->assertOk()
            ->assertSee('Hapus Semua')
            ->assertSee('history-checkbox', false);

        $this->actingAs($admin)
            ->from(route('monitoring.history'))
            ->delete(route('monitoring.history.destroy'), [
                'ids' => [$first->id],
            ])
            ->assertRedirect(route('monitoring.history'));

        $this->assertDatabaseMissing('qos_measurements', ['id' => $first->id]);
        $this->assertDatabaseHas('qos_measurements', ['id' => $second->id]);
        $this->assertDatabaseHas('qos_measurements', ['id' => $other->id]);

        $this->actingAs($admin)
            ->delete(route('monitoring.history.destroy'), [
                'delete_all' => 1,
                'school_id' => $schoolA->id,
            ])
            ->assertRedirect(route('monitoring.history', ['school_id' => $schoolA->id]));

        $this->assertDatabaseMissing('qos_measurements', ['id' => $second->id]);
        $this->assertDatabaseHas('qos_measurements', ['id' => $other->id]);
    }

    public function test_school_user_cannot_delete_another_school_measurement(): void
    {
        $school = $this->makeSchool('TEMP Delete Own');
        $otherSchool = $this->makeSchool('TEMP Delete Other');
        $user = $this->makeUser('sekolah', $school->id);

        $own = $this->makeMeasurement($school);
        $other = $this->makeMeasurement($otherSchool);

        $this->actingAs($user)
            ->delete(route('monitoring.history.destroy'), [
                'ids' => [$other->id],
            ])
            ->assertRedirect(route('monitoring.history'));

        $this->assertDatabaseHas('qos_measurements', ['id' => $other->id]);

        $this->actingAs($user)
            ->delete(route('monitoring.history.destroy'), [
                'ids' => [$own->id],
            ])
            ->assertRedirect(route('monitoring.history'));

        $this->assertDatabaseMissing('qos_measurements', ['id' => $own->id]);
    }

    public function test_notification_page_can_delete_selected_notifications(): void
    {
        $admin = $this->makeUser('admin');
        $school = $this->makeSchool('TEMP Delete Notification');
        $measurement = $this->makeMeasurement($school);

        $this->actingAs($admin)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Hapus Terpilih')
            ->assertSee('notification-checkbox', false);

        $this->actingAs($admin)
            ->delete(route('notifications.destroy'), [
                'ids' => [$measurement->id],
            ])
            ->assertRedirect(route('notifications.index'));

        $this->assertDatabaseMissing('qos_measurements', ['id' => $measurement->id]);
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

    private function makeMeasurement(School $school): QosMeasurement
    {
        return QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => 10,
            'delay' => 20,
            'jitter' => 2,
            'packet_loss' => 0,
            'ping' => 15,
            'success' => true,
            'measured_at' => now(),
        ]);
    }

    private function makeUser(string $role, ?int $schoolId = null): User
    {
        return User::create([
            'school_id' => $schoolId,
            'role' => $role,
            'name' => 'Delete Test ' . Str::random(5),
            'email' => 'delete-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);
    }
}
