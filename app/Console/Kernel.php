<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {




        $schedule->command('images:prune-cache')->daily()->withoutOverlapping();

        $schedule->command('queue:work --stop-when-empty --tries=3 --timeout=300 --max-time=50')
                 ->everyMinute()
                 ->withoutOverlapping(10)
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
