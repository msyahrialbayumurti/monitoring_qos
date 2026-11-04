<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Beri setiap akun tanpa sekolah sebuah sekolah pribadi.
        |--------------------------------------------------------------------------
        */

        $users = DB::table('users')
            ->whereNull('school_id')
            ->orderBy('id')
            ->get();

        foreach ($users as $user) {
            $schoolId = DB::table('schools')->insertGetId([
                'nama_sekolah' => $user->name ?: 'Akun ' . $user->id,
                'ip_target' => null,
                'bandwidth' => null,
                'paket_isp' => null,
                'status' => true,
                'agent_token' => Str::random(48),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')
                ->where('id', $user->id)
                ->update(['school_id' => $schoolId]);
        }

        /*
        |--------------------------------------------------------------------------
        | Semua akun memakai satu peran yang sama.
        |--------------------------------------------------------------------------
        */

        DB::table('users')->update(['role' => 'user']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `users` MODIFY `role` VARCHAR(255) NOT NULL DEFAULT 'user'"
            );
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `users` MODIFY `role` VARCHAR(255) NOT NULL DEFAULT 'sekolah'"
            );
        }
    }
};
