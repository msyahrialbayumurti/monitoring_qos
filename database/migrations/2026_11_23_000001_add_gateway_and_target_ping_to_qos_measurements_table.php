<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qos_measurements', function (Blueprint $table) {
            $table->decimal('gateway_ping', 10, 2)
                ->nullable()
                ->after('ping');

            $table->decimal('gateway_jitter', 10, 2)
                ->nullable()
                ->after('gateway_ping');

            $table->decimal('gateway_packet_loss', 10, 2)
                ->nullable()
                ->after('gateway_jitter');

            $table->boolean('gateway_success')
                ->nullable()
                ->after('gateway_packet_loss');

            $table->decimal('target_ping', 10, 2)
                ->nullable()
                ->after('gateway_success');

            $table->decimal('target_packet_loss', 10, 2)
                ->nullable()
                ->after('target_ping');

            $table->boolean('target_success')
                ->nullable()
                ->after('target_packet_loss');
        });
    }

    public function down(): void
    {
        Schema::table('qos_measurements', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_ping',
                'gateway_jitter',
                'gateway_packet_loss',
                'gateway_success',
                'target_ping',
                'target_packet_loss',
                'target_success',
            ]);
        });
    }
};
