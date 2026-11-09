<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('measurement_id')
                ->nullable()
                ->constrained('qos_measurements')
                ->nullOnDelete();

            $table->string('phone', 20);
            $table->string('event', 30);
            $table->string('condition', 20)->nullable();
            $table->text('message');
            $table->string('status', 20);
            $table->text('response')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(['school_id', 'created_at']);
            $table->index(['measurement_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notifications');
    }
};
