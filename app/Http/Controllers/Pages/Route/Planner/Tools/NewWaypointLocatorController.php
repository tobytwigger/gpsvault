<?php

namespace App\Http\Controllers\Pages\Route\Planner\Tools;

use Illuminate\Http\Request;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Location\Line;
use Location\Utility\PointToLineDistance;

class NewWaypointLocatorController
{
    const SAMPLE_SIZE = 100;

    public function __invoke(Request $request)
    {
        $request->validate([
            'geojson' => 'required|array|min:2',
            'geojson.*.lat' => 'required|numeric|min:-90|max:90',
            'geojson.*.lng' => 'required|numeric|min:-180|max:180',
            'lat' => 'required|numeric|min:-90|max:90',
            'lng' => 'required|numeric|min:-180|max:180',
        ]);



        $fullLinestring = $request->input('geojson');

        $smallestDistanceIndex = null;
        $smallestDistance = null;

        for ($i = 1; $i <= count($fullLinestring) - 1; $i++) {
            $segmentStart = new Coordinate(
                $fullLinestring[$i-1]['lat'],
                $fullLinestring[$i-1]['lng'],
            );
            $segmentEnd = new Coordinate(
                $fullLinestring[$i]['lat'],
                $fullLinestring[$i]['lng'],
            );

            $distance = (new PointToLineDistance(new Vincenty()))->getDistance(
                new Coordinate($request->input('lat'), $request->input('lng')),
                new Line($segmentStart, $segmentEnd)
            );

            if ($smallestDistance === null || $distance < $smallestDistance) {
                $smallestDistance = $distance;
                $smallestDistanceIndex = $i;
            }
        }

        return [
            'index' => $smallestDistanceIndex,
        ];
    }
}
