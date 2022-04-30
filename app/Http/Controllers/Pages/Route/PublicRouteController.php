<?php

namespace App\Http\Controllers\Pages\Route;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Inertia\Inertia;

class PublicRouteController extends Controller
{
    public function show(Route $route)
    {
        abort_if($route->public !== true, 403, 'This route is not public');

        return Inertia::render('Route/Public', [
            'routeModel' => $route->load('stats'),
        ]);
    }
}
