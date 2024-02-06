<?php

use App\Mail\MessageEmail;
use App\Services\CaseStudiesService;
use Illuminate\Support\Facades\Route;
use Google\Cloud\Storage\StorageClient;

use App\Mail\TestEmail;
use Illuminate\Support\Facades\Mail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

########################################### START LOCAL ROUTES
## THIS ROUTES ARE ONLY REGISTERED LOCALLY FOR DEV PORPOISES

if (app()->environment('local')) {

    Route::get('/db', function () {
        try {
            DB::connection()->getPdo();
            return "Success.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    Route::get('/cc', function () {

        // Clear route cache
        Artisan::call('route:clear');

        // Clear configuration cache
        Artisan::call('config:clear');

        // Clear application cache
        Artisan::call('cache:clear');

        return 'Caches cleared successfully!';
    });

    Route::get('/env', function () {

        // Get the env
        return env('APP_ENV');
    });

    // GET CSRF TOKEN
    Route::get('/csrf', function () {
        $token = csrf_token();
        return response()->json(['_token' => $token]);
    });

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

    // Test mail send basic
    Route::get('/mail-test', function () {

        // Testing if the email sending is done
        Mail::to(env('MAIL_USERNAME'))->send(new TestEmail());

        return 'Success!';
    });

    // Test mail send messages
    Route::get('/mail-test-message', function () {
        $data =[
            'name'     => 'test',
            'email'    => 'test@test.test',
            'subject'  => 'test',
            'content'  => 'test'
        ];

        Mail::to(env('MAIL_USERNAME'))
            ->send(new MessageEmail($data));

        return 'Success!';
    });
}

########################################### END LOCAL ROUTES

########################################### START CASE STUDIES ROUTES

// Rate limit on ( 60/2 requests per min )
Route::middleware('throttle:60,1')->group(function () {

    Route::get('/case-studies', function (CaseStudiesService $caseStudiesService) {
        $foldersWithFiles = $caseStudiesService->getCaseStudies();
        return view('case-studies.index', ['foldersWithFiles' => $foldersWithFiles]);
    });

    Route::get('/case-studies/file/{file}', function (CaseStudiesService $caseStudiesService, $file) {
        $htmlContent = $caseStudiesService->getFileContent($file);
        if ($htmlContent !== null) {
            return view('case-studies.example', ['htmlContent' => $htmlContent]);
        } else {
            abort(404);
        }
    });
});
########################################### END CASE STUDIES ROUTES

########################################### START PUBLIC ROUTES

// Rate limit on ( 20/2 requests per min )
Route::middleware('throttle:20,1')->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/details', function () {
        return view('details');
    });
});

########################################### END PUBLIC ROUTES
