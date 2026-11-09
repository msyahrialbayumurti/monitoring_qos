<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->timestamp('measure_requested_at')
                ->nullable()
                ->after('agent_version');

            $table->timestamp('agent_offline_notified_at')
                ->nullable()
                ->after('measure_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'measure_requested_at',
                'agent_offline_notified_at',
            ]);
        });
    }
};
