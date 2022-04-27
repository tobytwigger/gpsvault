<?php

namespace App\Http\Controllers\Pages\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats;
use App\Models\Waypoint;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;

class GeoJsonController extends Controller
{
    public function show(Stats $stats)
    {
        $this->authorize('view', $stats->model);

        $points = $stats->waypoints()
            ->whereNotNull('points')
            ->get()
            ->map(fn (Waypoint $waypoint) => new Coordinate($waypoint->latitude, $waypoint->longitude));

        $polyline = new Polyline();
        $polyline->addPoints($points->all());

        return $polyline->format(new GeoJSON());
    }
}
