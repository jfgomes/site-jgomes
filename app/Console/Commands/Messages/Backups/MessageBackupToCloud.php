<?php

namespace App\Console\Commands\Messages\Backups;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Storage\StorageClient;

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
        $path = base_path() . env('GC_HOST_PATH');
        if (!file_exists($path)) {
            mkdir($path, 775, true);
        }

        $file    = $path . env('GC_HOST_FILE');
        $timeAux = date("Y_m_d_H_i_s");
        $fileLog = "$path/messages-backup-" . $timeAux . ".json";

        $data = DB::table('messages')->get();

        // Create backups in server
        file_put_contents($file, json_encode($data));
        file_put_contents($fileLog, json_encode($data));

        // Backup limit to 24 files + 1 ( latest )
        exec("cd $path && ls -t | tail -n +25 | xargs -I {} rm {}");

        // Create cloud connection:
        $storage = new StorageClient([
            'keyFilePath' => base_path() . "/gc-" . env('APP_ENV') . ".json"
        ]);

        // Create bucket instance
        $bucket = $storage->bucket(env('APP_ENV') . "-backups-bd");

        // Update latest backup in cloud
        $bucket->upload(fopen($path . env('GC_CLOUD_FILE'), 'r'),
            ["name" => env('GC_CLOUD_PATH') . env('GC_CLOUD_FILE')]
        );

        // Create backup by hour in cloud
        $bucket->upload(fopen($path . "/messages-backup-" . $timeAux . ".json", 'r'),
            ["name" => env('GC_CLOUD_PATH') . "messages-backup-" . $timeAux . ".json"]
        );

        Log::channel('messages-backups')
            ->info('Backups done with success!');

        // Delete log > 2 days older
        $this->deleteOlderBackups($bucket);

        $this->info("Messages backup to cloud started with success..");
        return 0;

    }

    /**
     * @return void
     */
    private function deleteOlderBackups($bucket)
    {
        $dayBeforeYesterday             = Carbon::now()->subDays(2);
        $dayBeforeYesterdayBackupPrefix = 'messages-backup-' . $dayBeforeYesterday->format('Y_m_d_H');

        // List all objects in the bucket
        $objects = $bucket->objects();

        // Extract objects from the iterator
        $objectsArray = iterator_to_array($objects);

        // Filter objects based on the name prefix of the previous day's backups
        $oldBackups = array_filter($objectsArray, function ($object) use ($dayBeforeYesterdayBackupPrefix) {
            return strpos($object->name(), $dayBeforeYesterdayBackupPrefix) !== false;
        });

        // Delete all older backups
        foreach ($oldBackups as $oldBackup) {
            $oldBackup->delete();
            Log::channel('messages-backups')
                ->info('Deleted old backup ' . $oldBackup->name());
        }
    }
}
