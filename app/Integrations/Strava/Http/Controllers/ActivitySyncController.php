<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Integrations\Strava\Jobs\LoadStravaComments;
use App\Integrations\Strava\Jobs\LoadStravaKudos;
use App\Integrations\Strava\Jobs\LoadStravaPhotos;
use App\Integrations\Strava\Jobs\LoadStravaStats;
use App\Models\Activity;
use Illuminate\Support\Facades\Bus;

class ActivitySyncController extends Controller
{
    public function __invoke(Activity $activity)
    {
        $this->authorize('update', $activity);

        Bus::chain([
            new LoadStravaActivity($activity),
            new LoadStravaStats($activity),
            new LoadStravaComments($activity),
            new LoadStravaKudos($activity),
            new LoadStravaPhotos($activity),
        ])
            ->dispatch();
    }
}
