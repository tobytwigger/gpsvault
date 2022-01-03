<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ClientController extends Controller
{

    public function index()
    {
        return Inertia::render('Strava/Client/Index', [

        ]);
    }

}
