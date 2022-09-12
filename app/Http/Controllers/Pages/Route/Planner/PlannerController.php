<?php

namespace App\Http\Controllers\Pages\Route\Planner;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RoutePath;
use Illuminate\Http\Request;
use Inertia\Inertia;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;

class PlannerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Route::class);
    }

    public function create()
    {
        return Inertia::render('Route/Planner');
    }

    public function edit(Route $route)
    {
        return Inertia::render('Route/Planner', [
            'routeModel' => $route->append('path'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'geojson' => 'required|array|min:2',
            'geojson.*' => 'required|array',
            'geojson.*.lat' => 'required|numeric|min:-90|max:90',
            'geojson.*.lng' => 'required|numeric|min:-180|max:180',
            'geojson.*.alt' => 'required|numeric',
            'points' => 'sometimes|array',
            'points.*' => 'required|array',
            'points.*.lat' => 'required|numeric|min:-90|max:90',
            'points.*.lng' => 'required|numeric|min:-180|max:180',
            'points.*.place_id' => 'sometimes|nullable|numeric|exists:places,id',
            'distance' => 'sometimes|nullable|numeric|min:0',
            'elevation' => 'sometimes|nullable|numeric',
            'duration' => 'sometimes|nullable|numeric|min:0',
        ]);

        $route = Route::create([
            'name' => $request->input('name'),
        ]);

        $path = RoutePath::create([
            'route_id' => $route->id,
            'distance' => $request->input('distance', 0),
            'duration' => $request->input('duration', 0),
            'elevation_gain' => $request->input('elevation', 0),
            'linestring' => new LineString(array_map(fn (array $point) => new Point($point['lat'], $point['lng'], $point['alt']), $request->input('geojson'))),
        ]);

        foreach ($request->input('points', []) as $point) {
            $path->routePoints()->create([
                'location' => new Point($point['lat'], $point['lng']),
                'place_id' => $point['place_id'] ?? null,
            ]);
        }

        return redirect()->route('planner.edit', $route);
    }

    public function update(Request $request, Route $route)
    {
        $request->validate([
            'geojson' => 'required|array|min:2',
            'geojson.*' => 'required|array',
            'geojson.*.lat' => 'required|numeric|min:-90|max:90',
            'geojson.*.lng' => 'required|numeric|min:-180|max:180',
            'geojson.*.alt' => 'required|numeric',
            'points' => 'sometimes|array',
            'points.*' => 'required|array',
            'points.*.id' => 'sometimes|nullable|numeric|exists:route_points,id',
            'points.*.lat' => 'required|numeric|min:-90|max:90',
            'points.*.lng' => 'required|numeric|min:-180|max:180',
            'points.*.place_id' => 'sometimes|nullable|numeric|exists:places,id',
        ]);

        $path = RoutePath::create([
            'route_id' => $route->id,
            'distance' => $request->input('distance', 0),
            'elevation_gain' => $request->input('elevation', 0),
            'linestring' => new LineString(array_map(fn (array $point) => new Point($point['lat'], $point['lng'], $point['alt']), $request->input('geojson'))),
            'duration' => 0,
        ]);

        foreach ($request->input('points', []) as $point) {
            $attributes = [
                'location' => new Point($point['lat'], $point['lng']),
                'place_id' => $point['place_id'] ?? null,
            ];
            // If $point['id'] is set we can populate the new point with data from the given point ID.

            $path->routePoints()->create($attributes);
        }

        return redirect()->route('planner.edit', $route);
    }
}
