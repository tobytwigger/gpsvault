<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaceController extends Controller
{

    public function index()
    {
        return Inertia::render('Place/Index', [
            'places' => Place::orderBy('name')->paginate(request()->input('perPage', 8))
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
            'description' => 'sometimes|nullable|string|max:65535',
            'type' => 'required|in:food_drink,shops,amenities,tourist,accommodation,other',
            'url' => 'sometimes|nullable|url',
            'phone_number' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email',
            'address' => 'sometimes|nullable|string',
            'location' => 'required|array',
            'location.lat' => 'required|nullable|numeric',
            'location.lng' => 'required|nullable|numeric',
        ]);


        $validated['location'] = new \MStaack\LaravelPostgis\Geometries\Point($validated['location']['lat'], $validated['location']['lng']);

        $place->fill($validated);
        $place->save();

        return redirect()->back(fallback: route('place.show', $place));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
            'type' => 'required|in:food_drink,shops,amenities,tourist,accommodation,other',
            'url' => 'sometimes|nullable|url',
            'phone_number' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email',
            'address' => 'sometimes|nullable|string',
            'location' => 'required|array',
            'location.lat' => 'required|nullable|numeric',
            'location.lng' => 'required|nullable|numeric',
        ]);


        $validated['location'] = new \MStaack\LaravelPostgis\Geometries\Point($validated['location']['lat'], $validated['location']['lng']);

        $place = Place::create($validated);

        return redirect()->route('place.show', $place);
    }

}
