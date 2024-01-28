<?php

namespace App\Console\Commands\Messages\Backups;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MessageBackupToCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:messages-backup-to-cloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to get all the messages from db and create a backup to cloud';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = base_path() . "/storage/db-backups/";
        if (!file_exists($path)) {
            mkdir($path, 775, true);
        }

        $file      = "$path/messages-latest-backup.back";
        $file_back = "$path/messages-backup-" . date("Y_m_d_H_s_i") . ".back";

        $data = DB::table('messages')->get();

        file_put_contents($file, json_encode($data));
        file_put_contents($file_back, json_encode($data));

        // Backup limit to 10 files
        exec("cd $path && ls -t | tail -n +31 | xargs -I {} rm {}");

        Log::channel('messages-backups')
            ->info('Backup done with success!');

        return 0;

    }
}
