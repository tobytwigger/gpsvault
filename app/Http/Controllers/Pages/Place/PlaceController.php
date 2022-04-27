<?php

namespace App\Http\Controllers\Pages\Place;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        abort_if($place->user_id !== Auth::id(), 403, 'You do not own this place.');

        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
            'type' => 'sometimes|in:food_drink,shops,amenities,tourist,accommodation,other',
            'url' => 'sometimes|nullable|url',
            'phone_number' => 'sometimes|nullable|string',
            'email' => 'sometimes|nullable|email|max:255',
            'address' => 'sometimes|nullable|string|max:65535',
            'location' => 'sometimes|array',
            'location.lat' => 'required_with:location|numeric|min:-90|max:90',
            'location.lng' => 'required_with:location|numeric|min:-180|max:180',
        ]);


        if(array_key_exists('location', $validated)) {
            $validated['location'] = new \MStaack\LaravelPostgis\Geometries\Point($validated['location']['lat'], $validated['location']['lng']);
        }

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
            'email' => 'sometimes|nullable|email|max:255',
            'address' => 'sometimes|nullable|string|max:65535',
            'location' => 'required|array',
            'location.lat' => 'required|nullable|numeric|min:-90|max:90',
            'location.lng' => 'required|nullable|numeric|min:-180|max:180',
        ]);


        $validated['location'] = new \MStaack\LaravelPostgis\Geometries\Point($validated['location']['lat'], $validated['location']['lng']);

        $place = Place::create($validated);

        return redirect()->route('place.show', $place);
    }

}
