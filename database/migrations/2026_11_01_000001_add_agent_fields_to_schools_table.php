<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('agent_token', 64)
                ->nullable()
                ->unique()
                ->after('paket_isp');

            $table->timestamp('agent_last_seen_at')
                ->nullable()
                ->after('agent_token');

            $table->string('agent_gateway', 45)
                ->nullable()
                ->after('agent_last_seen_at');

            $table->string('agent_public_ip', 45)
                ->nullable()
                ->after('agent_gateway');

            $table->string('agent_isp', 255)
                ->nullable()
                ->after('agent_public_ip');

            $table->string('agent_version', 50)
                ->nullable()
                ->after('agent_isp');
        });

        DB::table('schools')
            ->whereNull('agent_token')
            ->orderBy('id')
            ->chunkById(100, function ($schools) {
                foreach ($schools as $school) {
                    DB::table('schools')
                        ->where('id', $school->id)
                        ->update([
                            'agent_token' => Str::random(48),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'agent_token',
                'agent_last_seen_at',
                'agent_gateway',
                'agent_public_ip',
                'agent_isp',
                'agent_version',
            ]);
        });
    }
};
