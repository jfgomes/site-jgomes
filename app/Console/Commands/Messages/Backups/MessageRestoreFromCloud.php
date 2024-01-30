<?php

namespace App\Console\Commands\Messages\Backups;

use Google\Cloud\Storage\StorageClient;
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
        // Create cloud connection:
        $storage = new StorageClient([
            'keyFilePath' => base_path() . "/gc-" . env('APP_ENV') . ".json"
        ]);

        // Create bucket instance
        $bucket = $storage->bucket(env('APP_ENV') . "-backups-bd");

        // Get backup from cloud
        $object = $bucket->object(env('GC_CLOUD_PATH') . env('GC_CLOUD_FILE'));
        $object->downloadToFile($backupFilePath = base_path() . env('GC_HOST_PATH') . "cloud-backup.json");

        // Load the content of the SQL file
        $jsonContent = file_get_contents($backupFilePath);

        // Decode the JSON into an associative array
        $dataJson = json_decode($jsonContent, true);

        // Check if we have data
        if (empty($dataJson)) {
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
                ->insert($dataJson);

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
