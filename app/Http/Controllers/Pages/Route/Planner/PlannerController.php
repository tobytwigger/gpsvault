<?php

namespace App\Http\Controllers\Pages\Route\Planner;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RoutePath;
use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\Valhalla\Valhalla;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            'geojson' => 'required|string',
            'waypoints' => 'sometimes|array',
            'waypoints.*' => 'required|array',
            'waypoints.*.lat' => 'required|numeric|min:-90|max:90',
            'waypoints.*.lng' => 'required|numeric|min:-180|max:180',
            'waypoints.*.place_id' => 'sometimes|nullable|numeric|exists:places,id',
            'distance' => 'sometimes|nullable|numeric|min:0',
            'elevation_gain' => 'sometimes|nullable|numeric',
            'duration' => 'sometimes|nullable|numeric|min:0',
        ]);

        $route = Route::create([
            'name' => $request->input('name'),
        ]);

        $linestring = GooglePolylineEncoder::decodeValue($request->input('geojson'), 6);
        $elevation = (new Valhalla())->elevationForLineString($request->input('geojson'));

        $path = RoutePath::create([
            'route_id' => $route->id,
            'distance' => $request->input('distance', 0),
            'duration' => $request->input('duration', 0),
            'elevation_gain' => $request->input('elevation_gain', 0),
            'linestring' => new LineString(Arr::map($linestring, fn (array $point, int $index) => new Point($point[0], $point[1], $elevation['range_height'][$index][1]))),
        ]);

        foreach ($request->input('waypoints', []) as $point) {
//            $path->routePoints()->create([
//                'location' => new Point($point['lat'], $point['lng']),
//                'place_id' => $point['place_id'] ?? null,
//            ]);
        }

        return redirect()->route('planner.edit', $route);
    }

    public function update(Request $request, Route $route)
    {
        $request->validate([
            'geojson' => 'required|string',
            'waypoints' => 'sometimes|array',
            'waypoints.*' => 'required|array',
            'waypoints.*.id' => 'sometimes|nullable|numeric|exists:route_points,id',
            'waypoints.*.lat' => 'required|numeric|min:-90|max:90',
            'waypoints.*.lng' => 'required|numeric|min:-180|max:180',
            'waypoints.*.place_id' => 'sometimes|nullable|numeric|exists:places,id',
            'distance' => 'sometimes|nullable|numeric|min:0',
            'elevation_gain' => 'sometimes|nullable|numeric',
            'duration' => 'sometimes|nullable|numeric|min:0',
        ]);

        $linestring = GooglePolylineEncoder::decodeValue($request->input('geojson'));
        $elevation = (new Valhalla())->elevationForLineString($request->input('geojson'));

        $path = RoutePath::create([
            'route_id' => $route->id,
            'distance' => $request->input('distance', 0),
            'duration' => $request->input('duration', 0),
            'elevation_gain' => $request->input('elevation', 0),
            'linestring' => new LineString(Arr::map($linestring, fn (array $point, int $index) => new Point($point[0], $point[1], $elevation['range_height'][$index][1]))),
        ]);

        foreach ($request->input('waypoints', []) as $point) {
//            $attributes = [
//                'location' => new Point($point['lat'], $point['lng']),
//                'place_id' => $point['place_id'] ?? null,
//            ];
            // If $point['id'] is set we can populate the new point with data from the given point ID.

//            $path->routePoints()->create($attributes);
        }

        return redirect()->route('planner.edit', $route);
    }
}
