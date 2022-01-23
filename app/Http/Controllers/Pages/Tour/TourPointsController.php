<?php

namespace App\Http\Controllers\Pages\Tour;

use App\Http\Controllers\Controller;
use App\Models\Tour;

class TourPointsController extends Controller
{

    public function show(Tour $tour)
    {
        $this->authorize('view', $tour);



        return $stats->points();
    }

}
