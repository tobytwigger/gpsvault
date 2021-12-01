<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Http\Controllers\ImportController;
use App\Integrations\Strava\Http\Controllers\StravaController;
use App\Integrations\Integration;
use App\Integrations\Strava\Http\Controllers\StravaFixController;
use App\Integrations\Strava\Import\Importer;
use App\Integrations\Strava\Import\Importers\ActivityImporter;
use App\Integrations\Strava\Import\StravaImporter;
use App\Integrations\Strava\Tasks\SaveNewActivities;
use App\Integrations\Strava\Tasks\StravaUpload;
use App\Integrations\Strava\Tasks\SyncRoutes;
use App\Services\Sync\Task;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StravaServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(StravaImporter::class);
    }

    public function boot()
    {
        Integration::registerIntegration('strava', StravaIntegration::class);
        Importer::registerImporter(ActivityImporter::class);
        Task::registerTask(SaveNewActivities::class);
        Task::registerTask(SyncRoutes::class);
        Task::registerTask(StravaUpload::class);
        Route::middleware(['web', 'auth:sanctum', 'verified'])->group(function() {
            Route::resource('import', ImportController::class)->only('show');
        });
        Route::middleware(['web', 'auth:sanctum', 'verified'])->prefix('strava')->group(function() {
            Route::get('login', [StravaController::class, 'login'])->name('strava.login');
            Route::get('callback', [StravaController::class, 'callback'])->name('strava.callback');
        });
    }

}
