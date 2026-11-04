<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['nama_sekolah' => 'Sekolah NETMONITOR'],
            [
                'status' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@netmonitor.local'],
            [
                'school_id' => $school->id,
                'role' => 'user',
                'name' => 'Pengguna NETMONITOR',
                'password' => 'admin12345',
            ]
        );
    }
}
