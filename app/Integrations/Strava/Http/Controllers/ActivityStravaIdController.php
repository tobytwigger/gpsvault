<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Integrations\Strava\Jobs\LoadStravaComments;
use App\Integrations\Strava\Jobs\LoadStravaKudos;
use App\Integrations\Strava\Jobs\LoadStravaPhotos;
use App\Integrations\Strava\Jobs\LoadStravaStats;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class ActivityStravaIdController extends Controller
{
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'strava_id' => 'required|numeric',
        ]);

        $this->authorize('update', $activity);

        $activity->setAdditionalData('strava_id', (int) $request->input('strava_id'));

        Bus::chain([
            new LoadStravaActivity($activity),
            new LoadStravaStats($activity),
            new LoadStravaComments($activity),
            new LoadStravaKudos($activity),
            new LoadStravaPhotos($activity),
        ])
            ->dispatch();

        return $activity;
    }
}
