<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StageOrderController extends Controller
{

    public function reorder(Request $request)
    {
        $request->validate([
            'stages' => 'required|array|min:1',
            'stages.*' => 'integer|exists:stages,id'
        ]);

        Stage::setNewOrder($request->input('stages'));

        return redirect()->route('tour.show', Stage::whereIn('id', $request->input('stages'))->firstOrFail()->tour_id);
    }
}
