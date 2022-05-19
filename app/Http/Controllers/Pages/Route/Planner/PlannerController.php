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
            'name' => 'required|string',
            'geojson' => 'required|array',
            'geojson.*' => 'required|array',
            'geojson.*.lat' => 'required|numeric|min:-90|max:90',
            'geojson.*.lng' => 'required|numeric|min:-180|max:180',
            'points' => 'required|array',
            'points.*' => 'required|array',
            'points.*.lat' => 'required|numeric|min:-90|max:90',
            'points.*.lng' => 'required|numeric|min:-180|max:180',
        ]);

        $route = Route::create([
            'name' => $request->input('name'),
        ]);

        $this->createRoutePath($request, $route);

        return redirect()->route('planner.edit', $route);
    }

    public function update(Request $request, Route $route)
    {
        $request->validate([
            'geojson' => 'required|array',
            'geojson.*' => 'required|array',
            'geojson.*.lat' => 'required|numeric|min:-90|max:90',
            'geojson.*.lng' => 'required|numeric|min:-180|max:180',
            'distance' => 'numeric|min:0',
            'points' => 'required|array',
            'points.*' => 'required|array',
            'points.*.lat' => 'required|numeric|min:-90|max:90',
            'points.*.lng' => 'required|numeric|min:-180|max:180',
        ]);

        $this->createRoutePath($request, $route);

        return redirect()->route('planner.edit', $route);
    }

    private function createRoutePath(Request $request, Route $route): RoutePath
    {
        $path = RoutePath::create([
            'route_id' => $route->id,
            'distance' => $request->input('distance'),
            'linestring' => new LineString(array_map(fn (array $point) => new Point($point['lat'], $point['lng']), $request->input('geojson'))),
        ]);

        foreach ($request->input('points') as $point) {
            $path->routePoints()->create([
                'location' => new Point($point['lat'], $point['lng']),
            ]);
        }

        return $path;
    }
}
