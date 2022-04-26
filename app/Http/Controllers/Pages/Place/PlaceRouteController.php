<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\PlaceRoute;
use App\Models\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaceRouteController extends Controller
{

    public function index(Route $route)
    {
        return $route->places()->paginate(request()->input('perPage', 8));
    }

    public function store(Request $request, Route $route)
    {
        $request->validate([
            'place_id' => 'required|numeric|exists:places,id'
        ]);

        PlaceRoute::create([
            'place_id' => $request->input('place_id'),
            'route_id' => $route->id
        ]);

        return redirect()->route('route.show', $route);
    }

    public function destroy(Route $route, Place $place)
    {
        PlaceRoute::where([
            'route_id' => $route->id,
            'place_id' => $place->id
        ])->delete();

        return redirect()->route('route.show', $route);
    }

}
