<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Commands\ResetRateLimit;
use App\Integrations\Strava\Commands\SyncStravaForUser;
use Illuminate\Support\ServiceProvider;

class StravaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([ResetRateLimit::class, SyncStravaForUser::class]);
    }

    public function boot()
    {
    }
}
