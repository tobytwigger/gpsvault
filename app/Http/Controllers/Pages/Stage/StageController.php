<?php

namespace App\Http\Controllers\Pages\Stage;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Stage;
use App\Models\Tour;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\UnauthorizedException;
use Inertia\Inertia;

class StageController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Tour::class, 'tour');
    }

    public function store(Request $request, Tour $tour)
    {
        if($tour->user_id !== Auth::id()) {
            throw new AuthorizationException(null, 403);
        }
        $stage = Stage::create([
            'tour_id' => $tour->id,
        ]);

        return redirect()->route('tour.show', $tour);
    }

    public function update(Request $request, Tour $tour, Stage $stage)
    {
        abort_if($tour->id !== $stage->tour_id, 404, 'The stage does not belong to the tour.');

        $validated = $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
            'date' => 'sometimes|nullable|date_format:Y-m-d',
            'is_rest_day' => 'sometimes|boolean',
            'route_id' => ['sometimes', 'nullable', 'integer', Rule::exists('routes', 'id')->where(fn($query) => $query->where('user_id', Auth::id()))],
            'activity_id' => ['sometimes', 'nullable', 'integer', Rule::exists('activities', 'id')->where(fn($query) => $query->where('user_id', Auth::id()))],
            'stage_number' => 'sometimes|integer|min:1'
        ]);

        $stage->fill($validated);
        $stage->save();
        if($request->has('stage_number') && $request->input('stage_number')) {
            $stage->setStageNumber((int) $request->input('stage_number'));
        }

        return redirect()->back(fallback: route('tour.show', $stage->tour_id));
    }

    public function destroy(Request $request, Tour $tour, Stage $stage)
    {
        abort_if($tour->id !== $stage->tour_id, 404, 'The stage does not belong to the tour.');

        $stage->delete();

        return redirect()->route('tour.show', $stage->tour_id);
    }

}
