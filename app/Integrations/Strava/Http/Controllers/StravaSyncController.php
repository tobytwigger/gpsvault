<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Jobs\SyncActivities;
use Illuminate\Support\Facades\Auth;

class StravaSyncController extends Controller
{
    public function __invoke()
    {
        SyncActivities::dispatch(Auth::user());

        return redirect()->route('integration.strava');
    }
}
