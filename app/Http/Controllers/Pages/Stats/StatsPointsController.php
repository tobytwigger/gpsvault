<?php

namespace App\Http\Controllers\Pages\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats;

class StatsPointsController extends Controller
{

    public function index(Stats $stats)
    {
        $this->authorize('view', $stats->model);

        return $stats->points();
    }

}
