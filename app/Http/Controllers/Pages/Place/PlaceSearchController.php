<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PlaceSearchController extends Controller
{

    public function search(Request $request)
    {
        $request->validate([
            'exclude_route_id' => 'sometimes|nullable|numeric|exists:routes,id'
        ]);

        return Place::when(
            $request->has('exclude_route_id') && $request->input('exclude_route_id'),
            fn(Builder $query) => $query->whereDoesntHave('routes',
                fn(Builder $subQuery) => $query->where('id', $request->input('exclude_route_id'))
            ))
            ->paginate(request()->input('perPage', 8));
    }

}
