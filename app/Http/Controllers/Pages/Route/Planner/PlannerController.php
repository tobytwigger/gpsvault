<?php

namespace App\Http\Controllers\Pages\Route\Planner;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Inertia\Inertia;

class PlannerController extends Controller
{

    public function create()
    {
        return Inertia::render('Route/Planner');
    }

    public function show(Route $route)
    {
        return Inertia::render('Route/Planner', [
            'route' => $route
        ]);
    }

}
