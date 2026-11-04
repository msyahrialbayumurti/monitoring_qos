<?php

namespace Tests\Feature;

use App\Models\QosMeasurement;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class SchoolDataIsolationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_only_sees_own_data_on_all_pages(): void
    {
        $ownSchool = $this->makeSchool('TEMP Isolation Own School');
        $otherSchool = $this->makeSchool('TEMP Isolation Other School');
        $user = $this->makeUser($ownSchool->id);

        $this->makeMeasurement($ownSchool, 11.11);
        $this->makeMeasurement($otherSchool, 99.99);

        $routes = [
            'dashboard',
            'monitoring.index',
            'monitoring.history',
            'qos.analysis',
            'recommendations.index',
            'notifications.index',
            'schools.index',
        ];

        foreach ($routes as $route) {
            $this->actingAs($user)
                ->get(route($route, ['school_id' => $otherSchool->id]))
                ->assertOk()
                ->assertSee($ownSchool->nama_sekolah)
                ->assertDontSee($otherSchool->nama_sekolah);
        }
    }

    public function test_user_cannot_open_another_school_data_page(): void
    {
        $ownSchool = $this->makeSchool('TEMP Isolation Own Edit');
        $otherSchool = $this->makeSchool('TEMP Isolation Other Edit');
        $user = $this->makeUser($ownSchool->id);

        $this->actingAs($user)
            ->get(route('schools.edit', $otherSchool))
            ->assertForbidden();
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

    private function makeMeasurement(School $school, float $throughput): QosMeasurement
    {
        return QosMeasurement::create([
            'school_id' => $school->id,
            'throughput' => $throughput,
            'delay' => 20,
            'jitter' => 2,
            'packet_loss' => 0,
            'ping' => 15,
            'success' => true,
            'measured_at' => now(),
        ]);
    }

    private function makeUser(?int $schoolId = null): User
    {
        return User::create([
            'school_id' => $schoolId,
            'role' => 'user',
            'name' => 'Isolation Test ' . Str::random(5),
            'email' => 'isolation-test-' . Str::random(10) . '@example.test',
            'password' => 'password',
        ]);
    }
}
