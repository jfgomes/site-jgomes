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
    public function handle(): int
    {
        // Create cloud connection
        $storage = new StorageClient([
            'keyFilePath' => base_path() . "/gc-" . env('APP_ENV') . ".json"
        ]);

        // Create bucket instance
        $bucket = $storage->bucket(env('APP_ENV') . "-backups-bd");

        // Get data from DB
        $data = DB::table('messages')->get();

        // Define the local path
        $path = base_path() . env('GC_HOST_PATH');
        $this->createPathIfNotExists($path);

        // Write data to a local file and get the file size
        $localFileSize = $this->writeDataToFile($data, $path);

        // Check if backup can be skipped
        if ($this->shouldSkipBackup($localFileSize, $bucket))
        {
            // Log no need message
            Log::channel('messages-backups')
                ->info("No need to do db backup as there's no new messages!");

            // I/O
            $this->info("No need to do db backup as there's no new messages..");
            return 0;
        }

        // Perform local backups
        $this->performLocalBackups($data, $path);

        // Perform cloud backups
        $timeAux = date("Y_m_d_H_i_s");
        $this->performCloudBackups($path, $timeAux);

        // Delete older backups in the cloud
        $this->deleteOlderBackups($bucket);

        // Backup path cleanup to keep the limit to 5 files + 1 ( latest )
        exec("cd $path && ls -t | tail -n +7 | xargs -I {} rm {}");

        // Log success
        Log::channel('messages-backups')
            ->info('Backups done with success!');

        // I/O
        $this->info("Messages backup to cloud done with success.");

        // Exit
        return 0;
    }

    /**
     * Create a directory if it does not exist.
     *
     * @param string $path
     * @return void
     */
    private function createPathIfNotExists(string $path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 775, true);
        }
    }

    /**
     * Write data to a temporary file and return the file size.
     *
     * @param mixed $data
     * @param string $path
     * @return int
     */
    private function writeDataToFile(mixed $data, string $path): int
    {
        $tmp_file = $path . '/tmp_file.json';
        file_put_contents($tmp_file, json_encode($data));
        $localFileSize = filesize($tmp_file);
        unlink($tmp_file);
        return $localFileSize;
    }

    /**
     * Check if the backup can be skipped based on file sizes.
     *
     * @param int $localFileSize
     * @param mixed $bucket
     * @return bool
     */
    private function shouldSkipBackup(int $localFileSize, mixed $bucket): bool
    {
        $objectName = env('GC_CLOUD_PATH') . env('GC_CLOUD_FILE');
        $latestBackupObject = $bucket->object($objectName);

        // Check if the object exists
        if ($latestBackupObject->exists())
        {
            // Retrieve the size of the latest backup object
            $latestBackupFileSize = $latestBackupObject->info()['size'];
        }
        else
        {
            // If the object doesn't exist, consider it as having size 0
            $latestBackupFileSize = 0;
        }

        // Compare sizes
        return $localFileSize <= $latestBackupFileSize;
    }

    /**
     * Perform local backups.
     *
     * @param mixed $data
     * @param string $path
     * @return void
     */
    private function performLocalBackups(mixed $data, string $path): void
    {
        $file    = $path . env('GC_HOST_FILE');
        $timeAux = date("Y_m_d_H_i_s");
        $fileLog = "$path/messages-backup-" . $timeAux . ".json";

        file_put_contents($file, json_encode($data));
        file_put_contents($fileLog, json_encode($data));
    }

    /**
     * Perform cloud backups.
     *
     * @param string $path
     * @param string $timeAux
     * @return void
     */
    private function performCloudBackups(string $path, string $timeAux): void
    {
        // Create a new storage client for cloud backups
        $storage = new StorageClient([
            'keyFilePath' => base_path() . "/gc-" . env('APP_ENV') . ".json"
        ]);

        // Create a bucket instance for cloud backups
        $bucket = $storage->bucket(env('APP_ENV') . "-backups-bd");

        // Upload the latest backup to the cloud
        $bucket->upload(fopen($path . env('GC_CLOUD_FILE'), 'r'), [
            'name' => env('GC_CLOUD_PATH') . env('GC_CLOUD_FILE'),
        ]);

        // Upload a backup by hour to the cloud
        $bucket->upload(fopen($path . "/messages-backup-" . $timeAux . ".json", 'r'), [
            'name' => env('GC_CLOUD_PATH') . "messages-backup-" . $timeAux . ".json",
        ]);
    }

    /**
     * Delete older backups from the cloud bucket.
     *
     * @param mixed $bucket
     * @return void
     */
    private function deleteOlderBackups(mixed $bucket): void
    {
        //$dayBeforeYesterday             = Carbon::now()->subDays(2);
        //$dayBeforeYesterdayBackupPrefix = 'messages-backup-' . $dayBeforeYesterday->format('Y_m_d_H');

        // Get the current time minus 5 hours
        $hours = Carbon::now()->subHour(5);
        $dayBeforeYesterdayBackupPrefix = 'messages-backup-' . $hours->format('Y_m_d_H');

        // List all objects in the bucket
        $objects = $bucket->objects();
        $objectsArray = iterator_to_array($objects);

        // Filter objects based on the name prefix of the previous day's backups
        $oldBackups = array_filter($objectsArray, function ($object) use ($dayBeforeYesterdayBackupPrefix) {
            return str_contains($object->name(), $dayBeforeYesterdayBackupPrefix);
        });

        // Delete all older backups
        foreach ($oldBackups as $oldBackup) {
            $oldBackup->delete();
            Log::channel('messages-backups')
                ->info('Deleted old backup ' . $oldBackup->name());
        }
    }
}
