<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityStats;
use App\Services\Analysis\Parser\Point;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Location\Formatter\Polyline\GeoJSON;
use Location\Polyline;

class StatsController extends Controller
{

    public function chart(ActivityStats $stats)
    {
        $this->authorize('view', $stats->activity);

        return $stats->points();
    }

    public function geojson(ActivityStats $stats)
    {
        $this->authorize('view', $stats->activity);

        $points = collect($stats->points())
            ->filter(fn(array $point) => ($point['latitude'] ?? null) !== null && ($point['longitude'] ?? null) !== null)
            ->map(fn(array $point) => new Coordinate($point['latitude'], $point['longitude']));

        $polyline = new Polyline();
        $polyline->addPoints($points->all());

        return $polyline->format(new GeoJSON());
    }

}
