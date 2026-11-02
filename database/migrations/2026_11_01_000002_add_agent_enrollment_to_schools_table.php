<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('agent_enrollment_token', 64)
                ->nullable()
                ->unique()
                ->after('agent_token');

            $table->timestamp('agent_enrollment_expires_at')
                ->nullable()
                ->after('agent_enrollment_token');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'agent_enrollment_token',
                'agent_enrollment_expires_at',
            ]);
        });
    }
};
