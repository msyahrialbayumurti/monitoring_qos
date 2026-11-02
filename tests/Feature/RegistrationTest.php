<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_does_not_require_network_fields(): void
    {
        $email = 'register-' . Str::random(8) . '@example.test';

        $response = $this->post('/register', [
            'name' => 'Penanggung Jawab Test',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nama_sekolah' => 'TEMP Register Test',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', $email)->firstOrFail();
        $school = School::findOrFail($user->school_id);

        $this->assertNull($school->ip_target);
        $this->assertNull($school->bandwidth);
        $this->assertNull($school->paket_isp);
    }
}
