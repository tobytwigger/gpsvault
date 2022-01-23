<?php

namespace App\Http\Controllers\Pages\Stats;

use App\Http\Controllers\Controller;
use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StatsPointsController extends Controller
{

    public function show(Stats $stats)
    {
        $this->authorize('view', $stats->model);

        return $stats->points();
    }

}
