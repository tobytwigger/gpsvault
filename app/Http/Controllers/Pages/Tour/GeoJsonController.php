<?php

namespace App\Http\Controllers\Pages\Tour;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use MStaack\LaravelPostgis\Geometries\Point;

class GeoJsonController extends Controller
{
    public function show(Tour $tour)
    {
        $this->authorize('view', $tour);

        $points = [];

        foreach ($tour->stages()->whereHas('route.routePaths')->get() as $stage) {
            $points = array_merge($points, $stage->route->path->linestring->getPoints());
        }

        return [
            'type' => 'LineString',
            'coordinates' => collect($points)->map(fn (Point $point) => [$point->getLng(), $point->getLat()]),
        ];
    }
}
