<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Activity;

class StatsController extends Controller
{

    public function index(Activity $activity)
    {
        $this->authorize('view', $activity);

        return $activity->stats;
    }

}
