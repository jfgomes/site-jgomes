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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/case-studies', function (CaseStudiesService $caseStudiesService) {
    $foldersWithFiles = $caseStudiesService->getCaseStudies();
    return view('case-studies.index', ['foldersWithFiles' => $foldersWithFiles]);
});

Route::get('/example/{file}', function (CaseStudiesService $caseStudiesService, $file) {
    $htmlContent = $caseStudiesService->getFileContent($file);
    if ($htmlContent !== null) {
        return view('case-studies.example', ['htmlContent' => $htmlContent]);
    } else {
        abort(404);
    }
});
