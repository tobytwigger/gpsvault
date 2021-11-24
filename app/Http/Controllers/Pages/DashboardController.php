<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Strava;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index(Strava $strava)
    {
        return Inertia::render('Dashboard/Dashboard');
    }

}
