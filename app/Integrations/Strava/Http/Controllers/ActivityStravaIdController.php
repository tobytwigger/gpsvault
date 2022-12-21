<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityStravaIdController extends Controller
{
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'strava_id' => 'required|numeric',
        ]);

        $this->authorize('update', $activity);

        $activity->setAdditionalData('strava_id', $request->input('strava_id'));

        LoadStravaActivity::dispatch($activity);

        return $activity;
    }
}
