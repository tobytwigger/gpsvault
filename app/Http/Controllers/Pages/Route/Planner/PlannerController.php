<?php

namespace App\Http\Controllers\Pages\Route\Planner;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlannerRequest;
use App\Models\Route;
use App\Models\RoutePath;
use App\Models\RoutePathWaypoint;
use App\Models\Waypoint;
use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\Valhalla\Valhalla;
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

    public function store(PlannerRequest $request)
    {
        $route = $this->saveRoute($request);

        return redirect()->route('planner.edit', $route);
    }

    public function update(PlannerRequest $request, Route $route)
    {
        $route = $this->saveRoute($request, $route);

        return redirect()->route('planner.edit', $route);
    }

    private function saveRoute(PlannerRequest $request, ?Route $route = null): Route
    {
        $route = $route ?? Route::create([
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

        $waypointOrder = [];
        foreach ($request->input('waypoints', []) as $index => $point) {
            $waypointAttributes = [
                'place_id' => $point['place_id'] ?? null,
                'location' => new Point($point['lat'], $point['lng']),
                'name' => $point['name'] ?? null,
                'notes' => $point['notes'] ?? null
            ];
            if (isset($point['id']) && $point['id']) {
                $waypoint = Waypoint::findOrFail($point['id']);
                $waypoint->fill($waypointAttributes)->save();
            } else {
                $waypoint = Waypoint::create($waypointAttributes);
            }

            $routePathWaypoint = RoutePathWaypoint::create([
                'route_path_id' => $path->id, 'waypoint_id' => $waypoint->id
            ]);
            $waypointOrder[] = $routePathWaypoint->id;
        }

        RoutePathWaypoint::setNewOrder($waypointOrder);

        return $route;
    }
}
