<?php

namespace App\Console;

use App\Console\Commands\Messages\MessagesFromRabbit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        MessagesFromRabbit::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run command to parse the messages
        $schedule->command('queue:messages')
            ->everyMinute();

        // Run command to do the messages backups to cloud
        $schedule->command('db:messages-backup-to-cloud')
            ->everyTwoHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
