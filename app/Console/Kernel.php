<?php

namespace App\Console;

use App\Console\Commands\checkClientComments;
use App\Console\Commands\sendupdatePermissionsReportToEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // checkClientComments::class,
        // sendupdatePermissionsReportToEmail::class,
    ];
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('app:check-client-comments')->daily();
        // $schedule->command('app:check-client-comments')->everyMinute();
        // $schedule->command('app:send-update-Permissions-Report-To-Email')->everyMinute();
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
