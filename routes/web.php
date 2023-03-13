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
Route::get('/contact', [\App\Http\Controllers\Pages\Public\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Pages\Public\ContactController::class, 'store'])->name('contact.store');

Route::get('/route/{route}/public', [\App\Http\Controllers\Pages\Route\PublicRouteController::class, 'show'])->name('route.public');

/* Documentation */
Route::redirect('/documentation', '/docs')->name('documentation');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    /* Dashboard */
    Route::get('/dashboard', [\App\Http\Controllers\Pages\Dashboard\DashboardController::class, 'index'])->name('dashboard');
//    Route::get('/dashboard/{dashboardId}', [\App\Http\Controllers\Pages\Dashboard\DashboardController::class, 'show'])->name('dashboard.show');

    /* Tours */
    Route::resource('tour', \App\Http\Controllers\Pages\Tour\TourController::class)->only(['index', 'store', 'show', 'destroy', 'update']);
    Route::resource('tour.stage', \App\Http\Controllers\Pages\Stage\StageController::class)->only(['store', 'update', 'destroy']);
    Route::post('/tour/{tour}/stage-wizard', \App\Http\Controllers\Pages\Stage\StageWizardController::class)->name('tour.stage.wizard');

    /* Places */
    Route::get('place/search', [\App\Http\Controllers\Pages\Place\PlaceSearchController::class, 'search'])->name('place.search');
    Route::resource('place', \App\Http\Controllers\Pages\Place\PlaceController::class)->only(['index', 'show', 'update', 'store', 'destroy']);

    /* Activities */
    Route::get('/activity/search', [\App\Http\Controllers\Pages\Activity\ActivitySearchController::class, 'search'])->name('activity.search');
    Route::resource('activity', \App\Http\Controllers\Pages\Activity\ActivityController::class)->only(['store', 'update', 'destroy', 'show', 'index']);
    Route::prefix('activity/{activity}')->group(function () {
        Route::get('download', [\App\Http\Controllers\Pages\Activity\ActivityDownloadController::class, 'downloadActivity'])->name('activity.download');
        Route::resource('file', \App\Http\Controllers\Pages\Activity\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update', 'store']);
    });
    Route::post('/activity/file/duplicate', [\App\Http\Controllers\Pages\Activity\ActivityDuplicateController::class, 'index'])->name('activity.file.duplicate');

    /* Routes */
    Route::get('/route/search', [\App\Http\Controllers\Pages\Route\RouteSearchController::class, 'search'])->name('route.search');
    Route::redirect('/route/planner', '/route/planner/create');
    Route::resource('route', \App\Http\Controllers\Pages\Route\RouteController::class)->only(['index', 'store', 'update', 'destroy', 'show']);
    Route::prefix('route/{route}')->group(function () {
        Route::get('download', [\App\Http\Controllers\Pages\Route\RouteDownloadController::class, 'downloadRoute'])->name('route.download');
        Route::resource('file', \App\Http\Controllers\Pages\Route\RouteFileController::class, ['as' => 'route'])->only(['destroy', 'update', 'store']);
    });

    /* Route Planner */
    Route::get('/route/planner/create', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'create'])->name('planner.create');
    Route::post('/route/planner/save', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'store'])->name('planner.store');
    Route::get('/route/planner/edit/{route}', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'edit'])->name('planner.edit');
    Route::patch('/route/planner/save/{route}', [\App\Http\Controllers\Pages\Route\Planner\PlannerController::class, 'update'])->name('planner.update');
    Route::post('/route/planner/plan', \App\Http\Controllers\Pages\Route\Planner\RoutingController::class)->name('planner.plan');
    Route::post('/route/planner/tools/new-waypoint-locator', \App\Http\Controllers\Pages\Route\Planner\Tools\NewWaypointLocatorController::class)->name('planner.tools.new-waypoint-locator');

    /* Backups */
    Route::resource('backup', \App\Http\Controllers\Pages\Backup\BackupController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['backup' => 'file']);

    /* Files */
    Route::get('file/{file}/download', [\App\Http\Controllers\Pages\File\FileDownloadController::class, 'download'])->name('file.download');
    Route::get('file/{file}/preview', [\App\Http\Controllers\Pages\File\FilePreviewController::class, 'preview'])->name('file.preview');

    /* Settings */
    Route::resource('settings', \App\Http\Controllers\Pages\Settings\SettingsController::class)->only(['index', 'store']);

    /* Strava */
    Route::get('/integration/strava', [\App\Integrations\Strava\Http\Controllers\StravaOverviewController::class, 'index'])->name('integration.strava');
    Route::middleware('can:manage-strava-clients')->group(function () {
        Route::resource('client', \App\Integrations\Strava\Http\Controllers\Client\ClientController::class, ['as' => 'strava'])->only(['store', 'update', 'destroy']);

        /* Import */
        Route::resource('import', \App\Integrations\Strava\Http\Controllers\Import\ImportController::class, ['as' => 'strava'])->only(['show', 'store']);

        /* Sync */
        Route::post('/sync', \App\Integrations\Strava\Http\Controllers\StravaSyncController::class)->name('strava.sync');
        Route::patch('/activity/{activity}/link', [\App\Integrations\Strava\Http\Controllers\ActivityStravaIdController::class, 'update'])->name('strava.activity.link.update');

        /* Client invitations */
        Route::middleware('link')->get('/client/{client}/accept', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'accept'])->name('strava.client.accept');
        Route::post('/client/{client}/invite', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'invite'])->name('strava.client.invite');
        Route::delete('/client/{client}/leave', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'leave'])->name('strava.client.leave');
        Route::delete('/client/{client}/remove', [\App\Integrations\Strava\Http\Controllers\Client\ClientInvitationController::class, 'remove'])->name('strava.client.remove');

        /* Testing a client */
        Route::post('/client/{client}/test', \App\Integrations\Strava\Http\Controllers\Client\ClientTestController::class)->name('strava.client.test');

        /* Client status */
        Route::post('/client/{client}/enable', [\App\Integrations\Strava\Http\Controllers\Client\ClientEnabledController::class, 'enable'])->name('strava.client.enable');
        Route::post('/client/{client}/disable', [\App\Integrations\Strava\Http\Controllers\Client\ClientEnabledController::class, 'disable'])->name('strava.client.disable');
        Route::post('/client/{client}/public', [\App\Integrations\Strava\Http\Controllers\Client\ClientVisibilityController::class, 'makePublic'])->name('strava.client.public');
        Route::post('/client/{client}/private', [\App\Integrations\Strava\Http\Controllers\Client\ClientVisibilityController::class, 'makePrivate'])->name('strava.client.private');
    });

    /* Client Authentication */
    Route::get('client/{client}/start-login', [\App\Integrations\Strava\Http\Controllers\Client\ClientAuthController::class, 'initiateLogin'])->name('strava.client.login.start');
    Route::get('client/{client}/login', [\App\Integrations\Strava\Http\Controllers\Client\ClientAuthController::class, 'login'])->name('strava.client.login');
    Route::post('client/{client}/logout', [\App\Integrations\Strava\Http\Controllers\Client\ClientAuthController::class, 'logout'])->name('strava.client.logout');

    Route::post('activity/{activity}/sync', \App\Integrations\Strava\Http\Controllers\ActivitySyncController::class)->name('strava.activity.sync');
});
