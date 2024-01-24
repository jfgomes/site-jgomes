<?php

use Illuminate\Support\Facades\Route;
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

}

########################################### END LOCAL ROUTES

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ports', function () {
    return view('ports');
});

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
