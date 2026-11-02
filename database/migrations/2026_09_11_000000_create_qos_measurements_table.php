<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qos_measurements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->decimal('throughput', 10, 2)
                ->nullable();

            $table->decimal('delay', 10, 2)
                ->nullable();

            $table->decimal('jitter', 10, 2)
                ->nullable();

            $table->decimal('packet_loss', 10, 2)
                ->nullable();

            $table->decimal('ping', 10, 2)
                ->nullable();

            $table->boolean('success')
                ->default(false);

            $table->text('raw_output')
                ->nullable();

            $table->timestamp('measured_at');

            $table->timestamps();

            $table->index(['school_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qos_measurements');
    }
};
