<?php

namespace App\Services\Routing\Strategies;

use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\Routing\RouteOptions;
use App\Services\Routing\RouteResult;
use App\Services\Routing\RouterStrategy;
use App\Services\Routing\Waypoint;
use App\Services\Valhalla\Valhalla;
use Illuminate\Support\Collection;

class ValhallaRouterStrategy implements RouterStrategy
{
    public function route(Collection $waypoints, RouteOptions $options): RouteResult
    {
        $result = (new Valhalla())->route(
            $waypoints->map(fn(Waypoint $waypoint) => [
                'lat' => $waypoint->getLat(),
                'lon' => $waypoint->getLng(),
                'type' => 'through'
            ])->all()
        );

        // What is the linestring returned in?
        $linestring = GooglePolylineEncoder::decodeValue($result['trip']['legs'][0]['shape'], 6);
        // Add in elevation
        $distance = $result['trip']['legs'][0]['summary']['length'] * 1000;
        $time = $result['trip']['legs'][0]['summary']['time'];

        $elevation = (new Valhalla())->elevationForLineString($result['trip']['legs'][0]['shape']);

        return new RouteResult(
            collect($linestring)->map(fn($location, $index) => [
                $location[0], $location[1], $elevation['range_height'][$index][1], $elevation['range_height'][$index][0] // [0] is the distance through the route!
            ])->all(),
            $distance,
            $time
        );
    }
}
