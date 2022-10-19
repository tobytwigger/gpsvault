<?php

namespace App\Http\Controllers\Pages\Stage;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Tour;
use Illuminate\Http\Request;

class StageWizardController extends Controller
{

    public function __invoke(Tour $tour, Request $request)
    {
        $request->validate([
            'total_days' => 'required|numeric|min:1|max:1000'
        ]);

        for($i=0;$i<$request->input('total_days');$i++) {
            Stage::create([
                'tour_id' => $tour->id,
            ]);
        }

        return redirect()->route('tour.show', $tour);
    }

}
