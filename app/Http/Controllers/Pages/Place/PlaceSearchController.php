<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaceSearchController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'exclude_route_id' => 'sometimes|nullable|numeric|exists:routes,id',
            'bounds' => 'sometimes|array',
            'bounds.southwest' => 'required_with:bounds|array',
            'bounds.southwest.lat' => 'required_with:bounds|numeric|min:-90|max:90',
            'bounds.southwest.lng' => 'required_with:bounds|numeric|min:-180|max:180',
            'bounds.northeast' => 'required_with:bounds|array',
            'bounds.northeast.lat' => 'required_with:bounds|numeric|min:-90|max:90',
            'bounds.northeast.lng' => 'required_with:bounds|numeric|min:-180|max:180',
        ]);

        return Place::when(
                $request->has('exclude_route_id') && $request->input('exclude_route_id'),
                fn(Builder $query) => $query->whereDoesntHave('routes',
                    fn(Builder $subQuery) => $subQuery->where('routes.id', $request->input('exclude_route_id'))
                ))
            ->when(
                $request->has('bounds'),
                // longitude min, latitude min, long mx, lat max
                fn(Builder $query) => $query->whereRaw(
                    'places.location && ST_MakeEnvelope(?, ?, ?, ?, 4326)', [
                    $request->input('bounds.southwest.lng'), $request->input('bounds.southwest.lat'), $request->input('bounds.northeast.lng'), $request->input('bounds.northeast.lat'), ])
            )
            ->orderBy('name')
            ->paginate(request()->input('perPage', 8));
    }

}
