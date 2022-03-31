<?php

namespace App\Http\Controllers\Pages\Tour;

use App\Http\Controllers\Controller;
use App\Models\Stats;
use App\Models\Tour;
use App\Models\Waypoint;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;

class GeoJsonController extends Controller
{

    public function show(Tour $tour)
    {
        $this->authorize('view', $tour);

        $polyline = new Polyline();
        foreach($tour->stages as $stage) {
            if($stage->route_id) {
                $points = $stage->route->stats()
                    ->orderByPreference()
                    ->first()
                    ?->waypoints()
                    ->whereNotNull('points')
                    ->get()
                    ->map(fn(Waypoint $waypoint) => new Coordinate($waypoint->latitude, $waypoint->longitude));

                $polyline->addPoints($points->all());
            }
        }

        return $polyline->format(new GeoJSON());
    }

}
