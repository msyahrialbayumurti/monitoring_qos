<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@netmonitor.local'],
            [
                'school_id' => null,
                'role' => 'admin',
                'name' => 'Administrator NETMONITOR',
                'password' => 'admin12345',
            ]
        );
    }
}
