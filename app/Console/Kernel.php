<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        MessagesFromRabbit::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Ensure cronlogs diz exists
        $logDirectory = storage_path('cronlogs');
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        // Run command to parse the messages
        $schedule->command('queue:messages')->everyMinute()
            ->appendOutputTo(storage_path('cronlogs/output_messages_consume_prod.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
