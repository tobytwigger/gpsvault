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
        [$linestring, $elevation, $distance, $time] = $this->getFullRoute($waypoints, $options);

        return new RouteResult(
            collect($linestring)->map(fn ($location, $index) => [
                $location[0], $location[1], $elevation[$index][1], [$index][0], // [0] is the distance through the route!
            ])->all(),
            $distance,
            $time
        );
    }

    private function getFullRoute(Collection $waypoints, RouteOptions $options)
    {
        $linestring = [];
        $distance = 0;
        $time = 0;

        $previous = null; // Will be set to the last point in the previous chunk

        foreach ($waypoints->chunk(30) as $chunkedWaypoints) {
            if($previous !== null) {
                $chunkedWaypoints->prepend($previous);
            }
            $result = (new Valhalla())->route(
                $chunkedWaypoints->map(fn (Waypoint $waypoint) => [
                    'lat' => $waypoint->getLat(),
                    'lon' => $waypoint->getLng(),
                    //                'type' => 'through'
                ])->all(),
                $options->toArray()
            );

            foreach ($result['trip']['legs'] ?? [] as $leg) {
                $linestring = array_merge($linestring, GooglePolylineEncoder::decodeValue($leg['shape'], 6));
                // Add in elevation
                $distance += $leg['summary']['length'] * 1000;
                $time += $leg['summary']['time'];
            }

            $previous = $chunkedWaypoints->last();
        }

        $elevation = [];
        foreach (collect($linestring)->chunk(1000) as $chunkedPoints) {

            $elevation = array_merge(
                $elevation,
                (new Valhalla())
                    ->elevationForLineString(GooglePolylineEncoder::encode($chunkedPoints->all(), 6))['range_height']
            );
        }

        return [$linestring, $elevation, $distance, $time];
    }
}
