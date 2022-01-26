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

/* Public */
Route::get('/', [\App\Http\Controllers\Pages\Public\PublicController::class, 'welcome'])->name('welcome');

/* Documentation */
Route::get('/documentation', fn() => \Illuminate\Support\Facades\Redirect::away(config('app.docs')))->name('documentation');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    /* Dashboard */
    Route::get('/dashboard', [\App\Http\Controllers\Pages\Dashboard\DashboardController::class, 'index'])->name('dashboard');

    /* Tours */
    Route::resource('tour', \App\Http\Controllers\Pages\Tour\TourController::class)->only(['index', 'store', 'show', 'destroy', 'update']);
    Route::resource('tour.stage', \App\Http\Controllers\Pages\Stage\StageController::class)->only(['store', 'update', 'destroy']);
    Route::get('/tour/{tour}/points', [\App\Http\Controllers\Pages\Tour\TourPointsController::class, 'show'])->name('tour.points');
    Route::get('/tour/{tour}/geojson', [\App\Http\Controllers\Pages\Tour\GeoJsonController::class, 'show'])->name('tour.geojson');

    /* Activities */
    Route::get('/activity/search', [\App\Http\Controllers\Pages\Activity\ActivitySearchController::class, 'search'])->name('activity.search');
    Route::resource('activity', \App\Http\Controllers\Pages\Activity\ActivityController::class)->only(['store', 'update', 'destroy', 'show', 'index']);
    Route::prefix('activity/{activity}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\Activity\ActivityDownloadController::class, 'downloadActivity'])->name('activity.download');
        Route::resource('file', \App\Http\Controllers\Pages\Activity\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update', 'store']);
    });
    Route::post('/activity/file/duplicate', [\App\Http\Controllers\Pages\Activity\ActivityDuplicateController::class, 'index'])->name('activity.file.duplicate');

    /* Routes */
    Route::get('/route/search', [\App\Http\Controllers\Pages\Route\RouteSearchController::class, 'search'])->name('route.search');
    Route::resource('route', \App\Http\Controllers\Pages\Route\RouteController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::prefix('route/{route}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\Route\RouteDownloadController::class, 'downloadRoute'])->name('route.download');
        Route::resource('file', \App\Http\Controllers\Pages\Route\RouteFileController::class, ['as' => 'route'])->only(['destroy', 'update', 'store']);
    });
    Route::post('/route/file/duplicate', [\App\Http\Controllers\Pages\Route\RouteDuplicateController::class, 'index'])->name('route.file.duplicate');

    /* Backups */
    Route::resource('backup', \App\Http\Controllers\Pages\Backup\BackupController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['backup' => 'file']);
    Route::post('backups/sync/{sync}/cancel', [\App\Http\Controllers\Pages\Backup\CancelBackupController::class, 'cancel'])->name('backup.sync.cancel');

    /* Files */
    Route::get('file/{file}/download', [\App\Http\Controllers\Pages\File\FileDownloadController::class, 'download'])->name('file.download');
    Route::get('file/{file}/preview', [\App\Http\Controllers\Pages\File\FilePreviewController::class, 'preview'])->name('file.preview');

    /* Settings */
    Route::resource('settings', \App\Http\Controllers\Pages\Settings\SettingsController::class)->only(['index', 'store']);

    /* Stats */
    Route::get('/stats/{stats}/points', [\App\Http\Controllers\Pages\Stats\StatsPointsController::class, 'show'])->name('stats.points');
    Route::get('/stats/{stats}/geojson', [\App\Http\Controllers\Pages\Stats\GeoJsonController::class, 'show'])->name('stats.geojson');

    /* Strava */
    Route::get('/integration/strava', [\App\Integrations\Strava\Http\Controllers\StravaOverviewController::class, 'index'])->name('integration.strava');
    Route::middleware('can:manage-strava-clients')->group(function() {
        Route::resource('client', \App\Integrations\Strava\Http\Controllers\Client\ClientController::class, ['as' => 'strava'])->only(['store', 'index', 'update', 'destroy']);

        /* Client invitations **/
        Route::middleware('link')->get('/client/{client}/accept', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'accept'])->name('strava.client.accept');
        Route::post('/client/{client}/invite', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'invite'])->name('strava.client.invite');
        Route::delete('/client/{client}/leave', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'leave'])->name('strava.client.leave');
        Route::delete('/client/{client}/remove', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'remove'])->name('strava.client.leave');

        /* Client status */
        Route::post('/client/{client}/enable', [\App\Integrations\Strava\Http\Controllers\Client\ClientEnabledController::class, 'enable'])->name('strava.client.enable');
        Route::post('/client/{client}/disable', [\App\Integrations\Strava\Http\Controllers\Client\ClientEnabledController::class, 'disable'])->name('strava.client.disable');
        Route::post('/client/{client}/public', [\App\Integrations\Strava\Http\Controllers\Client\ClientVisibilityController::class, 'makePublic'])->name('strava.client.public');
        Route::post('/client/{client}/private', [\App\Integrations\Strava\Http\Controllers\Client\ClientVisibilityController::class, 'makePrivate'])->name('strava.client.private');

        //            Route::get('client/{client}/login', [StravaController::class, 'login'])->name('strava.login');
//            Route::get('client/{client}/callback', [StravaController::class, 'callback'])->name('strava.callback');
    });

});

