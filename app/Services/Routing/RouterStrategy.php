<?php

namespace App\Services\Routing;

use Illuminate\Support\Collection;

interface RouterStrategy
{
    /**
     * Plan a route.
     *
     * @param Collection|Waypoint[] $waypoints
     * @param RouteOptions $options
     *
     * @return RouteResult
     */
    public function route(Collection $waypoints, RouteOptions $options): RouteResult;
}
