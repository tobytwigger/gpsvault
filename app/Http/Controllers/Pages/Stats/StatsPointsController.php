<?php

namespace App\Http\Controllers\Pages\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats;

class StatsPointsController extends Controller
{
    public function show(Stats $stats)
    {
        $this->authorize('view', $stats->model);

        return $stats->activityPoints()->get()->append(['latitude', 'longitude']);
    }
}
