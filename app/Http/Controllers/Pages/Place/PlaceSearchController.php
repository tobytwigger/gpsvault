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
            'southwest_lat' => 'required_with:southwest_lng,northeast_lat,northeast_lng|numeric|min:-90|max:90',
            'southwest_lng' => 'required_with:southwest_lat,northeast_lat,northeast_lng|numeric|min:-180|max:180',
            'northeast_lat' => 'required_with:southwest_lat,southwest_lng,northeast_lng|numeric|min:-90|max:90',
            'northeast_lng' => 'required_with:southwest_lat,southwest_lng,northeast_lat|numeric|min:-180|max:180',
        ]);

        return Place::when(
                $request->has('exclude_route_id') && $request->input('exclude_route_id'),
                fn(Builder $query) => $query->whereDoesntHave('routes',
                    fn(Builder $subQuery) => $subQuery->where('routes.id', $request->input('exclude_route_id'))
                ))
            ->when(
                $request->has(['southwest_lat', 'southwest_lng', 'northeast_lat', 'northeast_lng']),
                // longitude min, latitude min, long mx, lat max
                fn(Builder $query) => $query->whereRaw(
                    'places.location && ST_MakeEnvelope(?, ?, ?, ?, 4326)', [
                    $request->input('southwest_lng'), $request->input('southwest_lat'), $request->input('northeast_lng'), $request->input('northeast_lat'), ])
            )
            ->orderBy('name')
            ->paginate(request()->input('perPage', 8));
    }

}
