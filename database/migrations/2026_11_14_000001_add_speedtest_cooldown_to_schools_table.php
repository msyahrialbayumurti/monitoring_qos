<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->timestamp('agent_speedtest_cooldown_until')
                ->nullable()
                ->after('agent_offline_notified_at');

            $table->string('agent_speedtest_notice', 500)
                ->nullable()
                ->after('agent_speedtest_cooldown_until');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'agent_speedtest_cooldown_until',
                'agent_speedtest_notice',
            ]);
        });
    }
};
