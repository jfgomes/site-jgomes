<?php

namespace App\Console\Commands\Messages\Backups;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MessageRestoreFromCloud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:messages-restore-from-cloud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to get the latest backup from the bucket and restore the database';

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
        $backupFilePath = base_path() . "/storage/db-backups/messages-latest-backup.back";

        // Load the content of the SQL file
        $jsonContent = file_get_contents($backupFilePath);

        // Decode the JSON into an associative array
        $dataArray = json_decode($jsonContent, true);

        // Check if we have data
        if (empty($dataArray)) {
            Log::channel('messages-backups')
                ->error('No data to rollback! About!');
            return 0;
        }

        // Check if the 'messages' table exists before truncating
        if (!Schema::hasTable('messages')) {
            Log::channel('messages-backups')
                ->error('Table messages not exist! About!');
            return 0;
        }

        // Clean table
        DB::table('messages')->truncate();

        // Start transaction
        DB::beginTransaction();

        try {

            // Insert in bulk
            DB::table('messages')
                ->insert($dataArray);

            // Commit if everything is successful
            DB::commit();

            // Log success
            Log::channel('messages-backups')
                ->info('Backup restored successfully!');

        } catch (\Exception $e) {

            // Rollback only if something wrong
            DB::rollback();
            Log::channel('messages-backups')
                ->error($e->getMessage());
        }

        return 0;
    }
}
