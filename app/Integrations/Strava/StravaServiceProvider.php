<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Commands\ResetRateLimit;
use App\Integrations\Strava\Commands\SyncStravaForUser;
use App\Integrations\Strava\Import\Upload\Importer;
use App\Integrations\Strava\Import\Upload\Importers\ActivityImporter;
use App\Integrations\Strava\Import\Upload\Importers\PhotoImporter;
use App\Integrations\Strava\Import\Upload\StravaImporter;
use Illuminate\Support\ServiceProvider;

class StravaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([ResetRateLimit::class, SyncStravaForUser::class]);
        $this->app->singleton(StravaImporter::class);
        Importer::registerImporter(ActivityImporter::class);
        Importer::registerImporter(PhotoImporter::class);
    }

    public function boot()
    {
    }
}
