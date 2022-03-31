<?php

namespace App\Http\Controllers\Pages\Tour;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Waypoint;
use Location\Coordinate;

class TourPointsController extends Controller
{

    public function show(Tour $tour)
    {
        $this->authorize('view', $tour);

        $points = collect();
        foreach($tour->stages as $stage) {
            if($stage->route_id) {
                $points = $points->merge(
                    $stage->route->stats()
                        ->orderByPreference()
                        ->first()
                        ?->waypoints()
                        ->whereNotNull('points')
                        ->get()
                        ->append(['latitude', 'longitude'])
                    );
            }
        }

        return $points;
    }

}
