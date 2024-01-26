<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessagesController;
use App\Services\CaseStudiesService;

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
}

########################################### END LOCAL ROUTES
########################################### START CASE STUDIES ROUTES

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

########################################### END CASE STUDIES ROUTES

########################################### START PUBLIC ROUTES

Route::get('/', function () {
    return view('welcome');
});

Route::get('/details', function () {
    return view('details');
});

Route::post('/send',
    [
        MessagesController::class, 'send'
    ]
)->name('send');

########################################### END PUBLIC ROUTES
