<?php

use App\Http\Controllers\ApcController;
use App\Http\Controllers\MaintenanceController;
use App\Mail\MessageEmail;
use App\Mail\TestEmail;
use App\Models\LocationsPt;
use App\Models\User;
use App\Services\CaseStudiesService;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Hash;
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

$conditionalFlag  = env('APP_ROUTE_COOKIE_FLAG');
$hasSpecialCookie = Cookie::has($conditionalFlag) || app()->environment('local');

########################################### START COOKIE ROUTES
## THIS ROUTES ARE ONLY AVAILABLE UNDER A COOKIE OR IF THE ENV IS LOCAL

if (($conditionalFlag && $hasSpecialCookie)
    || app()->environment('local')
) {

    // CREATE USER FOR TEST
    Route::get('/create_test_user', function ()
    {
        // Verify if
        $existingUser = User::where('email', 'test@test.test')
            ->first();

        if ($existingUser)
        {
            // If the user already exists, exit
            return response()->json(
                [
                    'message' => 'User already exists'
                ],
                400
            );
        }

        $user           = new User();
        $user->name     = 'Zé Manel';
        $user->email    = 'test@test.test';
        $user->password = Hash::make('Test@123');
        $user->save();

        return response()->json(
            [
                'message' => 'User created. In prod dont forget to delete'
            ],
            400
        );
    });

    // CREATE USER FOR TEST
    Route::get('/create_test_admin_user', function ()
    {
        // Verify if
        $existingUser = User::where('email', 'admin@test.test')
            ->first();

        if ($existingUser)
        {
            // If the user already exists, exit
            return response()->json(
                [
                    'message' => 'Admin user already exists'
                ],
                400
            );
        }

        $user           = new User();
        $user->name     = 'Zé Manel Admin';
        $user->email    = 'admin@test.test';
        $user->role     = 'admin';
        $user->password = Hash::make('Test@123');
        $user->save();

        return response()->json(
            [
                'message' => 'Admin User created. In prod dont forget to delete'
            ],
            400
        );
    });

    // DELETE USER FOR TEST
    Route::get('/delete_test_user', function () {

        // Find the user by the test email
        $user = User::where('email', 'test@test.test')
            ->first();

        // If the user is not found, return a response indicating that the user was not found
        if (!$user) {
            return response()->json(
                [
                    'message' => 'User not found'
                ],
                404
            );
        }

        // If the user is found, delete the user
        $user->delete();

        // Return a response indicating that the user was deleted successfully
        return response()->json(
            [
                'message' => 'User deleted successfully'
            ],
            200
        );
    });

    // CLEANUP APCu + Redis
    Route::get('/cleanup_location_caches', function ()
    {
        // Get all location Ids
        $locations = LocationsPt::pluck('id')->toArray();

        // Ensure locations redis DB
        Redis::select(2);

        foreach ($locations as $locationId)
        {
            $key = 'location_pt_' . $locationId;

            // Deleting the testing value from APCu
            apcu_delete($key);

            // Deleting the testing value from Redis
            Redis::del($key);
        }
    });

    // CLEANUP JUST Redis
    Route::get('/reset_redis_cache_for_locations', function () {
        Redis::select(2);
        Redis::flushdb();
        return "done";
    });

    // CLEANUP JUST APCu ( on the frontend this is executed )
    Route::get('/reset_apcu_cache_for_locations', function () {
        foreach (new \APCUIterator("/^location_pt_/") as $counter) {
            echo "<pre>"; print_r("apcu_delete for key: " . $counter['key']);
            apcu_delete($counter['key']);
        }
        return "done";
    });

    // TEST APCu + Redis + BD + CACHE WITH LOAD BALANCE locally
    Route::get('/warmup_location_caches', function () {

        // Ignore.. this is just a note: ( ab -n 20 -c 10 http://127.0.0.1:8001/test_load_balance_cache_sys )

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

        if (count($locations) == 0)
        {
            dd('No data to cache on DB. 😖');
        }

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
                'keyFilePath' => base_path() . "/gc-" . env('GC_APP_ENV') . ".json"
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

// I18N public
Route::get('/language/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->to(env('APP_URL'));
})->name('lang.switch');;

########################################### END PUBLIC ROUTES

########################################### START SERVERLESS ROUTES

// Route to authenticate
Route::get('/login', function () {

    // The redirects can come with short messages in the url and we encode it to not show plain text
    $paramName      = key(request()->all());
    $decodedString  = base64_decode($paramName);
    $successMessage = null;
    $errorMessage   = null;

    if ($decodedString !== false)
    {
        parse_str($decodedString, $params);
        if (isset($params['b64']))
        {
            // Parse success messages
            if (isset($params['success'])){
                $successMessage = $params['success'];
            }

            // Parse error messages
            if (isset($params['error'])){
                $errorMessage = $params['error'];
            }
        }
    }

    return view('auth.login', [
        'successMessage' => $successMessage,
        'errorMessage'   => $errorMessage,
    ]);

})
    ->name('auth.login');

// Route for authenticated user
Route::get('/home', function () {
    return view('home.index');
})
    ->name('home.index');

// Locations
Route::get('/locations', function ()  use ($hasSpecialCookie) {
    return view('locations.index', [
        'hasSpecialCookie' => $hasSpecialCookie
    ]);
})
    ->name('locations.index');

// Route for 'admin' rule
Route::get('/admin', function () {
    return view('admin.index');
})
    ->name('admin.index');

// Route for '403' message
Route::get('/403', function () {
    return view('errors.403');
})
    ->name('error.403');

// Route for '429' message
Route::get('/429', function () {
    return view('errors.429');
})
    ->name('error.429');

// Route for I18N
Route::get('/lang', function () {
    return view('lang.index');
})
    ->name('lang.index');

// Route for users list
Route::get('/users', function () {
    return view('users.index');
})
    ->name('lang.index');
########################################### END SERVERLESS ROUTES
