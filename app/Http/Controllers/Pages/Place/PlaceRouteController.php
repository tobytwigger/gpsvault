<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\PlaceRoute;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaceRouteController extends Controller
{
    public function store(Request $request, Route $route)
    {
        $request->validate([
            'place_id' => 'required|numeric|exists:places,id',
        ]);

        abort_if($route->user_id !== Auth::id(), 403, 'You do not own this route.');
        abort_if($route->places()->where('places.id', $request->input('place_id'))->exists(), 404, 'The place is already attached to the route.');

        PlaceRoute::create([
            'place_id' => $request->input('place_id'),
            'route_id' => $route->id,
        ]);

        return redirect()->route('route.show', $route);
    }

    public function destroy(Route $route, Place $place)
    {
        abort_if($route->user_id !== Auth::id(), 403, 'You do not own this route.');
        abort_if(!$route->places()->where('places.id', $place->id)->exists(), 404, 'The place is not attached to the route.');

        PlaceRoute::where([
            'route_id' => $route->id,
            'place_id' => $place->id,
        ])->delete();

        return redirect()->route('route.show', $route);
    }
}
