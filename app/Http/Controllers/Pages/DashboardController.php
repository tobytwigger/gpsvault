<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Integrations\Dropbox\Client\Dropbox;
use Inertia\Inertia;

class DashboardController extends Controller
{

    public function index()
    {
//        dd(Dropbox::client(\Auth::user())->getSpaceUsage());
        return Inertia::render('Dashboard/Dashboard');
    }

}
