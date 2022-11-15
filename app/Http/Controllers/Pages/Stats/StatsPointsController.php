<?php

namespace App\Http\Controllers\Pages\Stats;

use App\Http\Controllers\Controller;
use App\Models\ActivityPoint;
use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StatsPointsController extends Controller
{
    public function show(Request $request, Stats $stats)
    {
        $request->validate([
            'fields' => [
                'sometimes', 'array',
            ],
            'fields.*' => [
                'string',
                Rule::in([
                    'points',
                    'elevation',
                    'time',
                    'cadence',
                    'temperature',
                    'heart_rate',
                    'speed',
                    'grade',
                    'battery',
                    'calories',
                    'cumulative_distance',
                ]),
            ],
        ]);

        $this->authorize('view', $stats->model);

        $ids = $stats->activityPoints()->orderBy('time')->select('id')->get();

        $n = $ids->count() / 50; // Keep all multiples of n

        $filteredIds = [];
        for ($i =0; $i < $ids->count(); $i+=$n) {
            $filteredIds[] = $ids[$i]->id;
        }

        return ActivityPoint::query()
            ->orderBy('time', 'ASC')
            ->whereIn('id', $filteredIds)
            ->select($request->input('fields', ['*']))
            ->get()
            ->when(
                data_get($request->get('fields', []), 'points'),
                fn ($collection) => $collection->append(['latitude', 'longitude'])
            );
    }
}
