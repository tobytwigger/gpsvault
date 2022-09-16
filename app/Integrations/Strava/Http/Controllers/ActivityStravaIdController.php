<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityStravaIdController extends Controller
{

    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'strava_id' => 'required|numeric'
        ]);

        $activity->setAdditionalData('strava_id', $request->input('strava_id'));

        return $activity;

    }

}
