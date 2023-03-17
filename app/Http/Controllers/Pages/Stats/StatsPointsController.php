<?php

namespace App\Http\Controllers\Pages\Stats;

use App\Http\Controllers\Controller;
use App\Models\ActivityPoint;
use App\Models\Stats;
use App\Services\Analysis\Parser\Point;
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

        $this->authorize('view', $stats->activity);

        $dataGenerators = [
            'elevation' => fn() => collect($stats->linestring->getPoints())->map(fn(\MStaack\LaravelPostgis\Geometries\Point $point) => $point->getAlt())->all(),
            'time' => fn() => $stats->time_data,
            'cadence' => fn() => $stats->cadence_data,
            'temperature' => fn() => $stats->temperature_data,
            'heart_rate' => fn() => $stats->heart_rate_data,
            'speed' => fn() => $stats->speed_data,
            'grade' => fn() => $stats->grade_data,
            'battery' => fn() => $stats->battery_data,
            'calories' => fn() => $stats->calories_data,
            'cumulative_distance' => fn() => $stats->cumulative_distance_data,
        ];

        $return = [];
        $count = null;
        foreach($request->input('fields', ['*']) as $field) {
            $data = call_user_func($dataGenerators[$field]);
//            if($count === null) {
//                $count = count($data);
//            }
            foreach($data as $key => $value) {
                // I want e.g. 150 points, and there may be 3000 points total. So 3000/150=20, so I want every 20th point.
//                if($key % ($count / 150) === 0 || $key === $count - 1 || $key === 0) {
                    $return[$key][$field] = $value;
//                }
            }
        }

        return $return;

    }
}
