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
            'name' => 'sometimes|nullable|string|max:255',
            'route_id' => 'sometimes|exists:routes,id',
            'tour_id' => 'required|exists:tours,id',
        ]);

        $stage = Stage::create([
            'name' => $request->input('name'),
            'route_id' => $request->input('route_id'),
            'tour_id' => $request->input('tour_id'),
        ]);

        return redirect()->route('tour.show', $stage->tour_id);
    }

}
