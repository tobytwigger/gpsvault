<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Events\NewStravaActivity;
use App\Integrations\Strava\Events\StravaActivityCommentsUpdated;
use App\Integrations\Strava\Events\StravaActivityKudosUpdated;
use App\Integrations\Strava\Events\StravaActivityPhotosUpdated;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Integrations\Strava\Http\Controllers\ImportController;
use App\Integrations\Strava\Http\Controllers\StravaController;
use App\Integrations\Integration;
use App\Integrations\Strava\Import\Importer;
use App\Integrations\Strava\Import\Importers\Importers\ActivityImporter;
use App\Integrations\Strava\Import\Importers\Importers\PhotoImporter;
use App\Integrations\Strava\Import\StravaImporter;
use App\Integrations\Strava\Listeners\IndexStravaActivity;
use App\Integrations\Strava\Listeners\IndexStravaActivityComments;
use App\Integrations\Strava\Listeners\IndexStravaActivityKudos;
use App\Integrations\Strava\Listeners\IndexStravaActivityPhotos;
use App\Integrations\Strava\Listeners\MarkActivityAsLoadingComments;
use App\Integrations\Strava\Listeners\MarkActivityAsLoadingDetails;
use App\Integrations\Strava\Listeners\MarkActivityAsLoadingKudos;
use App\Integrations\Strava\Listeners\MarkActivityAsLoadingPhotos;
use App\Integrations\Strava\Tasks\SaveNewActivities;
use App\Integrations\Strava\Tasks\StravaUpload;
use App\Services\Sync\Task;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Event;
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
        if(config('strava.enable_detail_fetching', true)) {
            // Load segments for an activity
            Event::listen(StravaActivityUpdated::class, MarkActivityAsLoadingDetails::class);
            Event::listen(StravaActivityUpdated::class, IndexStravaActivity::class);

            // Load comments for an activity
            Event::listen(StravaActivityCommentsUpdated::class, MarkActivityAsLoadingComments::class);
            Event::listen(StravaActivityCommentsUpdated::class, IndexStravaActivityComments::class);

            // Load kudos for an activity
            Event::listen(StravaActivityKudosUpdated::class, MarkActivityAsLoadingKudos::class);
            Event::listen(StravaActivityKudosUpdated::class, IndexStravaActivityKudos::class);

            // Load photos for an activity
            Event::listen(StravaActivityPhotosUpdated::class, MarkActivityAsLoadingPhotos::class);
            Event::listen(StravaActivityPhotosUpdated::class, IndexStravaActivityPhotos::class);
        }

        Integration::registerIntegration('strava', StravaIntegration::class);
        Importer::registerImporter(ActivityImporter::class);
        Importer::registerImporter(PhotoImporter::class);
        Task::registerTask(SaveNewActivities::class);
        Task::registerTask(StravaUpload::class);
        Route::middleware(['web', 'auth:sanctum', 'verified'])->group(function() {
            Route::resource('import', ImportController::class)->only('show');
        });
        Route::middleware(['web', 'auth:sanctum', 'verified'])->prefix('strava')->group(function() {
            Route::get('login', [StravaController::class, 'login'])->name('strava.login');
            Route::get('callback', [StravaController::class, 'callback'])->name('strava.callback');
        });

        \RateLimiter::for('strava', function(CallQueuedListener $job) {
            return Limit::perHour(70)->by($job->data[0]->activity->id);
        });
    }

}
