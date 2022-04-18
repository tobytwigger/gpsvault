<?php

namespace App\Http\Controllers\Pages\Places;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaceController extends Controller
{

    public function index()
    {
        return Inertia::render('Place/Index', [
            'places' => Place::paginate(request()->input('perPage', 8))
        ]);
    }

    public function show(Place $place)
    {
        return Inertia::render('Place/Show', [
            'place' => $place
        ]);
    }

    public function update(Request $request, Place $place)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:255',
            'description' => 'sometimes|nullable|string|max:65535'
        ]);

        $place->fill($validated);
        $place->save();

        return redirect()->back(fallback: route('place.show', $place));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:255',
            'description' => 'sometimes|nullable|string|max:65535'
        ]);

        $place = Place::create($validated);

        return redirect()->route('place.show', $place);
    }
}
