<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/route/{route}/public', [\App\Http\Controllers\Pages\Route\PublicRouteController::class, 'show'])->name('route.public');

/* Documentation */
Route::redirect('/documentation', '/docs')->name('documentation');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    /* Dashboard */
    Route::get('/dashboard', [\App\Http\Controllers\Pages\Dashboard\DashboardController::class, 'index'])->name('dashboard');

    /* Tours */
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->resource('tour', \App\Http\Controllers\Pages\Tour\TourController::class)->only(['index', 'store', 'show', 'destroy', 'update']);
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->resource('tour.stage', \App\Http\Controllers\Pages\Stage\StageController::class)->only(['store', 'update', 'destroy']);
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->get('/tour/{tour}/geojson', [\App\Http\Controllers\Pages\Tour\GeoJsonController::class, 'show'])->name('tour.geojson');

    /* Places */
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->get('place/search', [\App\Http\Controllers\Pages\Place\PlaceSearchController::class, 'search'])->name('place.search');
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->resource('place', \App\Http\Controllers\Pages\Place\PlaceController::class)->only(['index', 'show', 'update', 'store']);
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->resource('route.place', \App\Http\Controllers\Pages\Place\PlaceRouteController::class)->only(['store', 'destroy']);

    /* Activities */
    Route::get('/activity/search', [\App\Http\Controllers\Pages\Activity\ActivitySearchController::class, 'search'])->name('activity.search');
    Route::resource('activity', \App\Http\Controllers\Pages\Activity\ActivityController::class)->only(['store', 'update', 'destroy', 'show', 'index']);
    Route::prefix('activity/{activity}')->group(function () {
        Route::get('download', [\App\Http\Controllers\Pages\Activity\ActivityDownloadController::class, 'downloadActivity'])->name('activity.download');
        Route::resource('file', \App\Http\Controllers\Pages\Activity\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update', 'store']);
    });
    Route::post('/activity/file/duplicate', [\App\Http\Controllers\Pages\Activity\ActivityDuplicateController::class, 'index'])->name('activity.file.duplicate');

    /* Routes */
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->get('/route/search', [\App\Http\Controllers\Pages\Route\RouteSearchController::class, 'search'])->name('route.search');
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->resource('route', \App\Http\Controllers\Pages\Route\RouteController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->prefix('route/{route}')->group(function () {
        Route::get('download', [\App\Http\Controllers\Pages\Route\RouteDownloadController::class, 'downloadRoute'])->name('route.download');
        Route::resource('file', \App\Http\Controllers\Pages\Route\RouteFileController::class, ['as' => 'route'])->only(['destroy', 'update', 'store']);
    });

    /* Route Planner */
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->get('/route/planner/create', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'create'])->name('planner.create');
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->post('/route/planner/save', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'store'])->name('planner.store');
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->get('/route/planner/edit/{route}', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'edit'])->name('planner.edit');
    Route::middleware(\App\Http\Middleware\PageIncomplete::class)->patch('/route/planner/save/{route}', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'update'])->name('planner.update');

    /* Backups */
    Route::resource('backup', \App\Http\Controllers\Pages\Backup\BackupController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['backup' => 'file']);

    /* Files */
    Route::get('file/{file}/download', [\App\Http\Controllers\Pages\File\FileDownloadController::class, 'download'])->name('file.download');
    Route::get('file/{file}/preview', [\App\Http\Controllers\Pages\File\FilePreviewController::class, 'preview'])->name('file.preview');

    /* Settings */
    Route::resource('settings', \App\Http\Controllers\Pages\Settings\SettingsController::class)->only(['index', 'store']);

    /* Stats */
    Route::get('/stats/{stats}/points', [\App\Http\Controllers\Pages\Stats\StatsPointsController::class, 'show'])->name('stats.points');

    /* Strava */
    Route::get('/integration/strava', [\App\Integrations\Strava\Http\Controllers\StravaOverviewController::class, 'index'])->name('integration.strava');
    Route::middleware('can:manage-strava-clients')->group(function () {
        Route::resource('client', \App\Integrations\Strava\Http\Controllers\Client\ClientController::class, ['as' => 'strava'])->only(['store', 'index', 'update', 'destroy']);

        /* Client invitations */
        Route::middleware('link')->get('/client/{client}/accept', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'accept'])->name('strava.client.accept');
        Route::post('/client/{client}/invite', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'invite'])->name('strava.client.invite');
        Route::delete('/client/{client}/leave', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'leave'])->name('strava.client.leave');
        Route::delete('/client/{client}/remove', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'remove'])->name('strava.client.remove');

        /* Client status */
        Route::post('/client/{client}/enable', [\App\Integrations\Strava\Http\Controllers\Client\ClientEnabledController::class, 'enable'])->name('strava.client.enable');
        Route::post('/client/{client}/disable', [\App\Integrations\Strava\Http\Controllers\Client\ClientEnabledController::class, 'disable'])->name('strava.client.disable');
        Route::post('/client/{client}/public', [\App\Integrations\Strava\Http\Controllers\Client\ClientVisibilityController::class, 'makePublic'])->name('strava.client.public');
        Route::post('/client/{client}/private', [\App\Integrations\Strava\Http\Controllers\Client\ClientVisibilityController::class, 'makePrivate'])->name('strava.client.private');

    });

    /* Client Authentication */
    Route::get('client/{client}/login', [\App\Integrations\Strava\Http\Controllers\Client\ClientAuthController::class, 'login'])->name('strava.client.login');
    Route::post('client/{client}/logout', [\App\Integrations\Strava\Http\Controllers\Client\ClientAuthController::class, 'logout'])->name('strava.client.logout');

    Route::post('activity/{activity}/sync', \App\Integrations\Strava\Http\Controllers\ActivitySyncController::class)->name('strava.activity.sync');
});
