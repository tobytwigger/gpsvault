<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Strava;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index(Strava $strava)
    {
        dump($strava->client()->getActivities(1)[0]);
        dd($strava->client()->getActivity(6193576241));
        return Inertia::render('Dashboard/Dashboard');
    }

}
