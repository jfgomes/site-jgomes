![Google cloud logo](http://127.0.0.1:8000/images/cs/gc.png)

## Introduction

- This project has a database based on a Docker service.

- Although it has data persistence, the storage is located in a volume that may not be 100% secure for some reason. If the volume is corrupted or accidentally deleted, the data in it may be lost forever. Therefore, it is necessary to ensure that there is additional security so that the data is not lost in case of an incident, and quick solutions for disaster recovery are required.

- For this case study, the chosen strategy is to back up the data to the cloud, specifically using Google Cloud as the provider.

- There is an hourly schedule, tailored to the project's needs, which in this case (to avoid unnecessary resource consumption) involves creating backups every 2 hours.

- These backups remain active for 2 days.

- There is always one backup that is the most recent, and it is this backup that restoration focuses on.

## DB Backup/Restore diagram

![Google cloud Backup/Restore diagram](http://jgomes.site/images/diagrams/backup-restore.drawio.png)

## Configuration Laravel side

- In LOCAL env, it should have a gc-local.json
- In PROD env, it should have a gc-prod.json

- Both env config files should be like:

```
        {
            "type": "service_account",
            "project_id": "resolute-world-392017",
            "private_key_id": "bed27175876f7741af55665e31d3df716f7d461e",
            "private_key": "${GC_PRIVATE_KEY}",
            "client_email": "db-backups-local@resolute-world-392017.iam.gserviceaccount.com",
            "client_id": "100008630709907137502",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/db-backups-local%40resolute-world-392017.iam.gserviceaccount.com",
            "universe_domain": "googleapis.com"
        }
```

- The env var ${GC_PRIVATE_KEY} should be at:

- LOCAL: file .env.dev
```
        GC_CLOUD_FILE='messages-latest-backup.json'
        GC_CLOUD_PATH='jgomes.site/messages/'
        GC_HOST_FILE='messages-latest-backup.json'
        GC_HOST_PATH='/storage/db-backups/'
        GC_PRIVATE_KEY='-----BEGIN PRIVATE KEY-----xxxxxxxxxxxxxxxxxxxxxxxxxxxxx-----END PRIVATE KEY-----\n'
```

- PROD: file .env
```
        GC_CLOUD_FILE='messages-latest-backup.json'
        GC_CLOUD_PATH='jgomes.site/messages/'
        GC_HOST_FILE='messages-latest-backup.json'
        GC_HOST_PATH='/storage/db-backups/'
        GC_PRIVATE_KEY='-----BEGIN PRIVATE KEY-----xxxxxxxxxxxxxxxxxxxxxxxxxxxxx-----END PRIVATE KEY-----\n'
```

NOTE: both .env var files are not in the repo.

## Implementation for backup to GC

- To test this command locally just run: php artisan db:messages-backup-to-cloud
- In prod this command runs automatically and the register is done in the Laravel Commands Kernel, like this:

```
      protected function schedule(Schedule $schedule): void
      {
            // Run command to do the messages backups to cloud
            $schedule->command('db:messages-backup-to-cloud')
                     ->everyTwoHours();
      }
```

#### Command:
```
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
            // Get the current time minus 5 hours
            $dayBeforeYesterday             = Carbon::now()->subDays(2);
            $dayBeforeYesterdayBackupPrefix = 'messages-backup-' . $dayBeforeYesterday->format('Y_m_d_H');
    
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
```

## Implementation for restore from GC

- To test this command locally just run: db:messages-restore-from-cloud
- In prod this command is not set automatic. It runs manually.


#### Command:
```
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
    public function handle(): int
    {
        // Create cloud connection:
        $storage = new StorageClient([
            'keyFilePath' => base_path() . "/gc-" . env('APP_ENV') . ".json"
        ]);

        // Create bucket instance
        $bucket = $storage->bucket(env('APP_ENV') . "-backups-bd");

        // Get backup from cloud
        $object = $bucket->object(env('GC_CLOUD_PATH') . env('GC_CLOUD_FILE'));

        // Check if exists in the cloud
        if (!$object->exists())
        {
            // Log
            Log::channel('messages-backups')
                ->error(env('GC_CLOUD_FILE') . ' file does not exist in the cloud!');

            // I/O
            $this->error(env('GC_CLOUD_FILE') . ' file does not exist in the cloud..');

            // Abort
            return 0;
        }

        $object->downloadToFile($backupFilePath = base_path() . env('GC_HOST_PATH') . "cloud-backup.json");

        // Load the content of the SQL file
        $jsonContent = file_get_contents($backupFilePath);

        // Delete tmp backup
        unlink($backupFilePath);

        // Decode the JSON into an associative array
        $dataJson = json_decode($jsonContent, true);

        // Check if we have data
        if (empty($dataJson))
        {
            Log::channel('messages-backups')
                ->error('No data to rollback! About!');

            // I/O
            $this->info("No data to rollback! About..");
            return 0;
        }

        // Check if the 'messages' table exists before truncating
        if (!Schema::hasTable('messages'))
        {
            Log::channel('messages-backups')
                ->error('Table messages not exist! About!');

            // I/O
            $this->info("Table messages not exist! About..");
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

            // I/O
            $this->info("Backup restored successfully..");

        } catch (\Exception $e) {

            // Rollback only if something wrong
            DB::rollback();

            // Log
            Log::channel('messages-backups')
                ->error($e->getMessage());

            // I/O
            $this->error($e->getMessage());
        }

        // Exit
        return 0;
    }
}
```

## Test full GC process

- This is in routes file and is only possible to run out of any verification in LOCAL.
- In PROD, the system needs a special cookie to do this test. By default, this route is hidden and the users can't access it.
- The code has comments that describe the tests that this block does.
```
    // Test bucket connection to GC
    Route::get('/bucket-test', function () {

        try {

            $localPath = env('GC_HOST_PATH');
            $localFile = env('GC_HOST_FILE');
            $cloudPath = env('GC_CLOUD_PATH');
            $cloudFile = env('GC_CLOUD_FILE');

            // TEST CONNECTION
            $storage   = new StorageClient([
                'keyFilePath' => base_path() . "/gc-" . env('APP_ENV') . ".json"
            ]);

            echo '<pre> - Connection done with success!';

            // TEST BUCKET
            $bucketName = env('APP_ENV') . "-backups-bd";
            $bucket = $storage->bucket($bucketName);

            echo '<pre> - Bucket test done with success!';

            // Filepath
            $filepath = base_path() . $localPath . $localFile;
            if (!is_file($filepath))
            {
                // If file not exist, create a dummy one
                $contentArray = ['test' => 'Test content'];
                file_put_contents($filepath, json_encode($contentArray, JSON_PRETTY_PRINT));
            }

            $object = $bucket->object($cloudPath . $cloudFile);

            // TEST FILTER - Calculate the name prefix for the previous day's backups but change according the needs
            $previousDayBackupPrefix = 'messages-backup-'; // . Carbon::yesterday()->format('Y_m_d');

            echo "<pre> - Filter test start: ( filter by '$previousDayBackupPrefix' ) ";

            // List all objects in the bucket
            $objects = $bucket->objects();

            // Extract objects from the iterator
            $objectsArray = iterator_to_array($objects);

            // Filter objects based on the name prefix of the previous day's backups
            $oldBackups = array_filter($objectsArray, function ($object) use ($previousDayBackupPrefix) {
                return str_contains($object->name(), $previousDayBackupPrefix);
            });

            foreach ($oldBackups as $oldBackup) {
                echo "<pre> ------ " . $oldBackup->name();
                //$oldBackup->delete();
            }

            echo "<pre> - Filter test done with success!";

            // TEST UPLOAD
            $bucket->upload(fopen($filepath, 'r'),
                ["name" => $cloudPath . $cloudFile]
            );

            echo '<pre> - Upload test done with success!';

            // TEST DOWNLOAD
            $object->downloadToFile(base_path() . $localPath . $localFile . "-from-gc");

            echo '<pre> - Download test done with success!';

            // TEST DELETE
            $object->delete();
            unlink(base_path() . $localPath . $localFile . "-from-gc");

            echo '<pre> - Delete done with success!';
            return '<pre> - Tests ended!';

        } catch(Exception $e) {
           dd($e->getMessage());
        }

    });
```

## Demonstration ( Click on the image to watch the video )
[![Demonstration video](https://jgomes.site/images/cs/git-branch-protection-video-thumbnail.jpg)](http://www.youtube.com/watch?v=Mt3wbPhwz5o)
