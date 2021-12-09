<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Integrations\Dropbox\Client\Dropbox;
use App\Integrations\Strava\Client\Strava;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index(Strava $strava)
    {
        dd($strava->client()->getActivityData(6193576241));
        return Inertia::render('Dashboard/Dashboard');
    }

}
