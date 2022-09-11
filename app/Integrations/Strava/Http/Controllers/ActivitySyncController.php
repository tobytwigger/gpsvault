<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Integrations\Strava\Jobs\LoadStravaComments;
use App\Integrations\Strava\Jobs\LoadStravaKudos;
use App\Integrations\Strava\Jobs\LoadStravaPhotos;
use App\Integrations\Strava\Jobs\LoadStravaStats;
use App\Models\Activity;

class ActivitySyncController extends Controller
{

    public function __invoke(Activity $activity)
    {
        LoadStravaActivity::dispatch($activity);
        LoadStravaStats::dispatch($activity);
        LoadStravaComments::dispatch($activity);
        LoadStravaKudos::dispatch($activity);
        LoadStravaPhotos::dispatch($activity);
    }

}
