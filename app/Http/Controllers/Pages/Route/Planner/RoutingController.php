<?php

namespace App\Http\Controllers\Pages\Route\Planner;

use App\Http\Requests\RoutingRequest;
use App\Services\Routing\RouteOptions;
use App\Services\Routing\Router;
use App\Services\Routing\Waypoint;

class RoutingController
{

    public function __invoke(RoutingRequest $request)
    {
        $waypoints = collect($request->input('waypoints'))->map(fn($waypoint) => new Waypoint($waypoint['location'][1], $waypoint['location'][0]));
        $result = Router::route($waypoints, new RouteOptions());

        return $result->toArray();
    }

}
