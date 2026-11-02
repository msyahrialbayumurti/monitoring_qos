<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            'ALTER TABLE `schools` MODIFY `ip_target` VARCHAR(45) NULL, MODIFY `bandwidth` DECIMAL(10,2) NULL'
        );
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            "UPDATE `schools` SET `ip_target` = COALESCE(`ip_target`, '0.0.0.0'), `bandwidth` = COALESCE(`bandwidth`, 0)"
        );

        DB::statement(
            'ALTER TABLE `schools` MODIFY `ip_target` VARCHAR(45) NOT NULL, MODIFY `bandwidth` DECIMAL(10,2) NOT NULL'
        );
    }
};
