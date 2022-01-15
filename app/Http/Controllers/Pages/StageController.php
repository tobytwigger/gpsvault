<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use Illuminate\Http\Request;

class StageController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        $stage = Stage::create([
            'tour_id' => $request->input('tour_id'),
        ]);

        return redirect()->route('tour.show', $stage->tour_id);
    }

    public function update(Request $request, Stage $stage)
    {
        $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
            'date' => 'sometimes|nullable|date',
            'is_rest_day' => 'sometimes|boolean',
            'route_id' => 'sometimes|nullable|exists:routes,id',
            'activity_id' => 'sometimes|nullable|exists:activities,id',
            'stage_number' => 'sometimes|integer'
        ]);

        $stage->fill($request->only([
            'name', 'description', 'date', 'is_rest_day', 'route_id', 'activity_id'
        ]));
        $stage->save();
        if($request->has('stage_number') && $request->input('stage_number')) {
            $stage->setStageNumber((int) $request->input('stage_number'));
        }

        return redirect()->route('tour.show', $stage->tour_id);
    }

    public function destroy(Request $request, Stage $stage)
    {
        $stage->delete();

        return redirect()->route('tour.show', $stage->tour_id);
    }

}
