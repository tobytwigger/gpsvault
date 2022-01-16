<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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


/* Documentation */
Route::get('/documentation', fn() => \Illuminate\Support\Facades\Redirect::away(config('app.docs')))->name('documentation');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    /* Dashboard */
    Route::get('/dashboard', [\App\Http\Controllers\Pages\Dashboard\DashboardController::class, 'index'])->name('dashboard');

    /* Tours */
    Route::resource('tour.stage', \App\Http\Controllers\Pages\Stage\StageController::class)->only(['store', 'update', 'destroy']);

    /* Activities */
    Route::get('/activity/search', [\App\Http\Controllers\Pages\Activity\ActivitySearchController::class, 'search'])->name('activity.search');
    Route::resource('activity', \App\Http\Controllers\Pages\Activity\ActivityController::class)->only(['store', 'update', 'destroy', 'show', 'index']);
    Route::prefix('activity/{activity}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\Activity\ActivityDownloadController::class, 'downloadActivity'])->name('activity.download');
        Route::resource('file', \App\Http\Controllers\Pages\Activity\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update', 'store']);
    });





    // UNTESTED


    Route::resource('sync', \App\Http\Controllers\Pages\SyncController::class)->only(['index', 'store', 'destroy']);
    Route::get('file/{file}/download', [\App\Http\Controllers\Pages\FileController::class, 'download'])->name('file.download');
    Route::get('file/{file}/preview', [\App\Http\Controllers\Pages\FileController::class, 'preview'])->name('file.preview');


    Route::get('/activity/stats/{stats}/chart', [\App\Http\Controllers\Api\StatsController::class, 'chart'])->name('activity.stats.chart');
    Route::get('/activity/stats/{stats}/geojson', [\App\Http\Controllers\Api\StatsController::class, 'geojson'])->name('activity.stats.geojson');
    Route::post('/activity/file/duplicate', [\App\Http\Controllers\Api\ActivityDuplicateController::class, 'index'])->name('activity.file.duplicate');

    Route::resource('backups', \App\Http\Controllers\Pages\BackupController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['backups' => 'file']);
    Route::delete('backups/sync/{sync}/cancel', [\App\Http\Controllers\Pages\BackupController::class, 'cancelSync'])->name('backups.sync.cancel');

    Route::resource('settings', \App\Http\Controllers\Pages\SettingsController::class)->only(['index', 'store']);

    Route::get('/route/search', [\App\Http\Controllers\Api\RouteController::class, 'search'])->name('route.search');
    Route::resource('route', \App\Http\Controllers\Pages\RouteController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::prefix('route/{route}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\RouteDownloadController::class, 'downloadRoute'])->name('route.download');
        Route::resource('file', \App\Http\Controllers\Pages\RouteFileController::class, ['as' => 'route'])->only(['destroy', 'update', 'store']);
    });
    Route::post('/route/file/duplicate', [\App\Http\Controllers\Api\RouteDuplicateController::class, 'index'])->name('route.file.duplicate');
    Route::get('/route/stats/{stats}/geojson', [\App\Http\Controllers\Api\StatsController::class, 'geojsonRoute'])->name('route.stats.geojson');

    Route::resource('tour', \App\Http\Controllers\Pages\TourController::class)->only(['index', 'store', 'show', 'destroy']);


    Route::get('/integration/{integration}/login', [\App\Http\Controllers\Pages\IntegrationLoginController::class, 'login'])->name('integration.login');
    Route::delete('/integration/{integration}', [\App\Http\Controllers\Pages\IntegrationController::class, 'destroy'])->name('integration.destroy');
    Route::get('/integration/strava', [\App\Http\Controllers\Pages\IntegrationController::class, 'strava'])->name('integration.strava');
    Route::get('/integration/{integration}/logs', [\App\Http\Controllers\Pages\ConnectionLogController::class, 'index'])->name('integration.logs');

});

Route::get('/', [\App\Http\Controllers\Pages\PublicController::class, 'welcome'])->name('home');
