<?php

namespace App\Console;

use App\Console\Commands\checkClientComments;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        checkClientComments::class,
    ];
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:check-client-comments')->everyMinute();
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
