<?php

namespace App\Console;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('qos:monitor')
            ->everyMinute()
            ->when(function () {
                $interval = (int) (
                    Setting::where('key', 'monitoring_interval')
                        ->value('value') ?? 5
                );

                if (! in_array($interval, [1, 5, 10, 15], true)) {
                    $interval = 5;
                }

                return now()->minute % $interval === 0;
            })
            ->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
