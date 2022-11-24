<?php

namespace App\Services\Routing\Strategies;

use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\Routing\RouteOptions;
use App\Services\Routing\RouteResult;
use App\Services\Routing\RouterStrategy;
use App\Services\Routing\Waypoint;
use App\Services\Valhalla\Valhalla;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ValhallaRouterStrategy implements RouterStrategy
{
    public function route(Collection $waypoints, RouteOptions $options): RouteResult
    {
        [$linestring, $elevation, $distance, $time, $elevationGain] = $this->getFullRoute($waypoints, $options);

        return new RouteResult(
            collect($linestring)->map(fn ($location, $index) => [
                $location[0], $location[1], $elevation[$index][1], $elevation[$index][0], // [0] is the distance through the route!
            ])->all(),
            $distance,
            $time,
            $elevationGain
        );
    }

    private function getFullRoute(Collection $waypoints, RouteOptions $options)
    {
        $linestring = [];
        $distance = 0;
        $time = 0;

        $previous = null; // Will be set to the last point in the previous chunk

        foreach ($waypoints->chunk(30) as $chunkedWaypoints) {
            if ($previous !== null) {
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
                $polyline = GooglePolylineEncoder::decodeValue($leg['shape'], 6);
                $linestring = array_merge($linestring, GooglePolylineEncoder::decodeValue($leg['shape'], 6));
                // Add in elevation
                $distance += $leg['summary']['length'] * 1000;
                $time += $leg['summary']['time'];
            }

            $previous = $chunkedWaypoints->last();
        }

        $elevation = [];
        $cumulativeDistance = 0.0;
        foreach (collect($linestring)->chunk(1000) as $index => $chunkedPoints) {
            $newElevation = (new Valhalla())
                ->elevationForLineString(GooglePolylineEncoder::encode($chunkedPoints->all(), 6))['range_height'];
            $modifiedElevation = array_map(function ($elevationItem) use ($cumulativeDistance) {
                $elevationItem[0] = $elevationItem[0] + $cumulativeDistance;

                return $elevationItem;
            }, $newElevation);
            $cumulativeDistance += Arr::last($newElevation)[0];

            $elevation = array_merge($elevation, $modifiedElevation);
        }

        $elevationGain = array_reduce($elevation, function ($elevationData, $elevationItem) {
            if ($elevationData['previous'] !== null) {
                $gain = $elevationItem[1] - $elevationData['previous'];
                if ($gain > 0) {
                    $elevationData['gain'] += $gain;
                }
            }
            $elevationData['previous'] = $elevationItem[1];

            return $elevationData;
        }, ['previous' => null, 'gain' => 0.0])['gain'];

        return [$linestring, $elevation, $distance, $time, $elevationGain];
    }
}
