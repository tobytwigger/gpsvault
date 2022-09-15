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
        $waypoints = collect($request->input('waypoints'))->map(fn($waypoint) => new Waypoint($waypoint['location']['lat'], $waypoint['location']['lng']));
        $result = Router::route($waypoints, new RouteOptions(
            costingOptions: [
                'bicycle' => [
                    'bicycle_type' => 'Road', // 'Hybrid', 'Cross', 'Mountain'
//                    'cycling_speed' => '20',
                    'use_roads' => $request->input('use_roads') ?? 0.5,
                    'use_hills' => $request->input('use_hills') ?? 0.5,
                ]
            ]
        ));

        return $result->toArray();
    }

}
