<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        /*
        |--------------------------------------------------------------------------
        | Setiap akun memiliki interval monitoring sendiri.
        | Perintah dijalankan tiap menit, lalu memeriksa apakah sebuah sekolah
        | sudah waktunya diukur berdasarkan interval pemiliknya.
        |--------------------------------------------------------------------------
        */
        $schedule->command('qos:monitor')
            ->everyMinute()
            ->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
