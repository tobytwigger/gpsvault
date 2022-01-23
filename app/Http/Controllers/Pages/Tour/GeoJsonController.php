<?php

namespace App\Http\Controllers\Pages\Tour;

use App\Http\Controllers\Controller;
use App\Models\Stats;
use App\Models\Tour;
use Location\Coordinate;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;

class GeoJsonController extends Controller
{

    public function show(Tour $tour)
    {
        $this->authorize('view', $tour);

        $points = collect($stats->points())
            ->filter(fn(array $point) => ($point['latitude'] ?? null) !== null && ($point['longitude'] ?? null) !== null)
            ->map(fn(array $point) => new Coordinate($point['latitude'], $point['longitude']));

        $polyline = new Polyline();
        $polyline->addPoints($points->all());

        return $polyline->format(new GeoJSON());
    }

}
