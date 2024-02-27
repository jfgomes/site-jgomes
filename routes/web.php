<?php

use App\Http\Controllers\ApcController;
use App\Http\Controllers\MaintenanceController;
use App\Mail\MessageEmail;
use App\Mail\TestEmail;
use App\Models\LocationsPt;
use App\Services\CaseStudiesService;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

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

## THIS ROUTES ARE ONLY AVAILABLE IF THE ENV IS LOCAL
if (app()->environment('local'))
{
    // CLEANUP TEST APCu + Redis
    Route::get('/cleanup_location_caches', function ()
    {
        // Get all location Ids
        $locations = LocationsPt::pluck('id')->toArray();

        // Ensure locations redis DB
        Redis::select(2);

        foreach ($locations as $locationId)
        {
            $key = 'location_pt_-' . $locationId;

            // Deleting the testing value from APCu
            apcu_delete($key);

            // Deleting the testing value from Redis
            Redis::del($key);
        }
    });

    // TEST APCu + Redis + BD + CACHE WITH LOAD BALANCE locally
    Route::get('/warmup_location_caches', function () {

        // Ignore.. this just a note: ( ab -n 20 -c 10 http://127.0.0.1:8001/test_load_balance_cache_sys )

        // Run frontend 1 ( php artisan serve --port=8001 )
        // Run frontend 2 ( php artisan serve --port=8002 )

        // Test 1 with in frontend 1 ( http://127.0.0.1:8001/test_load_balance_cache_sys )
        // This must go to DB and populate Redis and APCu for the frontend 1

        // Test 2 with in frontend 2 ( http://127.0.0.1:8002/test_load_balance_cache_sys )
        // This must go to Redis and populate APCu for the frontend 2

        // Test 3 with again in frontend 1 ( http://127.0.0.1:8001/test_load_balance_cache_sys )
        // This must have the values in APCu for the frontend 1

        // Test 4 with again in frontend 2 ( http://127.0.0.1:8002/test_load_balance_cache_sys )
        // This must have the values in APCu for the frontend 2

        // Get all location Ids
        $locations = LocationsPt::pluck('id')->toArray();

        // Ensure tests redis DB
        Redis::select(2);

        foreach ($locations as $locationId) {

            $key = "location_pt_" . $locationId;

            // Go to APCu and check if the value key exists
            $value = apcu_fetch($key);
            if (apcu_exists($key))
            {
                echo "<pre>APCu: $value\n";

                // Iterate
                continue;
            }

            // Go to Redis and check if the value key exists. Case exists save in APCu.
            $value = Redis::get($key);
            if ($value)
            {
                echo "<pre>Redis: $value\n";

                // Save in APCu
                apcu_store($key, $value);

                // Iterate
                continue;
            }

            // Go to DB and check if the value exists. Case exists save in Redis and APCu.
            $value = LocationsPt::find($locationId);
            if ($value)
            {
                $value = json_encode($value);
                echo "<pre>DB: $value\n";

                // Save in Redis
                Redis::set($key, $value);

                // Save in APCu
                apcu_store($key, $value);

                // Iterate
                continue;
            }

            echo "<pre>Location not found!\n";
        }

        return '<pre>Test concluded!';

    });
}

########################################### START COOKIE ROUTES
## THIS ROUTES ARE ONLY AVAILABLE UNDER A COOKIE OR IF THE ENV IS LOCAL
$conditionalFlag = env('APP_ROUTE_COOKIE_FLAG');
if (($conditionalFlag && Cookie::has($conditionalFlag))
    || app()->environment('local')
) {
    // APCu DASHBOARD PAGE
    Route::get('/apcu', [ApcController::class, 'index']);

    // TEST APCu
    Route::get('/test_apcu', function () {

        // Checking in APCu extension is loaded
        if (extension_loaded('apcu'))
        {
            echo "<pre>APCu is up!\n";

        } else {

            dd('APCu is down. 😖');
        }

        // Save the testing value to APCu
        echo "<pre>Trying to store the value 'testing_value' in APCu.. success!\n";
        apcu_store('key', 'testing_value');

        // Recovering the testing value from APCu
        $value = apcu_fetch('key');

        // Check if the testing key exists in APCu
        if (apcu_exists('key'))
        {
            echo "<pre>Trying to get your value from APCu: $value.. success!\n";

        } else {

            dd('APCu is not storing the test value. 😖');
        }

        // Deleting the testing value from APCu
        apcu_delete('key');
        echo "<pre>Trying to delete your value from APCu.. success!\n";

        // Return success!
        return '<pre>APCu is up and running successfully! 👍';
    });

    // CHECK DB CONNECTION
    Route::get('/db', function () {
        try {
            DB::connection()->getPdo();
            return "Success.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    // CLEAN CACHES
    Route::get('/cc', function () {

        // Clear route cache
        Artisan::call('route:clear');

        // Clear configuration cache
        Artisan::call('config:clear');

        // Clear application cache
        Artisan::call('cache:clear');

        return 'Caches cleared successfully!';
    });

    // GET ENV
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
                $oldBackup->delete();
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

    // Maintenance activate. Site down.
    Route::get('/deactivate',
        [
            MaintenanceController::class, 'deactivate'
        ]
    )->name('deactivate');

    // Maintenance deactivate. Site up.
    Route::get('/activate',
        [
            MaintenanceController::class, 'activate'
        ]
    )->name('activate');
}

########################################### END COOKIE ROUTES

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
