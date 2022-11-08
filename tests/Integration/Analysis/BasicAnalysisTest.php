<?php

namespace Tests\Integration\Analysis;

use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class BasicAnalysisTest extends BaseAnalysisTestCase
{

    /** @test */
    public function todo_finish_commented_points(){
        $this->markTestIncomplete();
    }

    public function getFileName(): string
    {
        return 'analysis1.json';
    }

    public function providesPointsToAnalyse(): array
    {
        return [
            'Start Latitude' => ['startLatitude', 52.055602, 52.055604],
            'Start Longitude' => ['startLongitude', -0.6997, -0.6995],
            'End Latitude' => ['endLatitude', 52.025594, 52.025596],
            'End Longitude' => ['endLongitude', -0.80107, -0.80105],
            'Distance' => ['distance', 9220, 9240],
            'Duration' => ['duration', 1980, 1990],
            'Started At' => ['startedAt', Carbon::make('2022-11-05 00:05:08')],
            'Finished At' => ['finishedAt', Carbon::make('2022-11-05 00:38:10')],
            'Average Cadence' => ['averageCadence', null],
            'Average Heartrate' => ['averageHeartrate', null],
            'Average Temp' => ['averageTemp', null],
            'Max Altitude' => ['maxAltitude', null],
            'Min Altitude' => ['minAltitude', null],
            'Average Pace' => ['averagePace', 0.20, 0.22],
            'Cumulative Elevation Gain' => ['cumulativeElevationGain', null],
            'Cumulative Elevation Loss' => ['cumulativeElevationLoss', null],
            'Max Heartrate' => ['maxHeartrate', null],
            'Calories' => ['calories', null],
            'Average Watts' => ['averageWatts', null],
            'Kilojoules' => ['kilojoules', null],
//            'Average Speed' => ['averageSpeed', null],
//            'Moving Time' => ['movingTime', 1960, 1970],
            'Max Speed' => ['maxSpeed', null],

            'Points 0 - Latitude' => ['latitude', 52.055603, null, 0, 52.055603],
            'Points 0 - Longitude' => ['longitude', -0.6996, null, 0, -0.6996],
            'Points 0 - Time' => ['time', Carbon::make('2022-11-05 00:05:08'), null, 0, Carbon::make('2022-11-05 00:05:08')],
//            'Points 0 - Cumulative Distance' => ['cumulativeDistance', -0.80106, null, 0],
            'Points 0 - Elevation' => ['elevation', null, null, 0],
            'Points 0 - Cadence' => ['cadence', null, null, 0],
            'Points 0 - Temperature' => ['temperature', null, null, 0],
            'Points 0 - Heart Rate' => ['heartRate', null, null, 0],
            'Points 0 - Speed' => ['speed', null, null, 0],
            'Points 0 - Grade' => ['grade', null, null, 0],
            'Points 0 - Battery' => ['battery', null, null, 0],
            'Points 0 - Calories' => ['calories', null, null, 0],

            'Points 100 - Latitude' => ['latitude', 52.054214, null, 100, 52.054214],
            'Points 100 - Longitude' => ['longitude', -0.701401, null, 100, -0.701401],
//            'Points 100 - Cumulative Distance' => ['cumulativeDistance', -0.80106, null, 100],
            'Points 100 - Time' => ['time', Carbon::make('2022-11-05 00:06:48'), null, 100, Carbon::make('2022-11-05 00:06:48')],
            'Points 100 - Elevation' => ['elevation', null, null, 100],
            'Points 100 - Cadence' => ['cadence', null, null, 100],
            'Points 100 - Temperature' => ['temperature', null, null, 100],
            'Points 100 - Heart Rate' => ['heartRate', null, null, 100],
            'Points 100 - Speed' => ['speed', null, null, 100],
            'Points 100 - Grade' => ['grade', null, null, 100],
            'Points 100 - Battery' => ['battery', null, null, 100],
            'Points 100 - Calories' => ['calories', null, null, 100],

            'Points 1000 - Latitude' => ['latitude', 52.038109, null, 1000, 52.038109],
            'Points 1000 - Longitude' => ['longitude', -0.750949, null, 1000, -0.750949],
            'Points 1000 - Time' => ['time', Carbon::make('2022-11-05 00:22:02'), null, 1000, Carbon::make('2022-11-05 00:22:02')],
//            'Points 1000 - Cumulative Distance' => ['cumulativeDistance', -0.80106, null, 1000],
            'Points 1000 - Elevation' => ['elevation', null, null, 1000],
            'Points 1000 - Cadence' => ['cadence', null, null, 1000],
            'Points 1000 - Temperature' => ['temperature', null, null, 1000],
            'Points 1000 - Heart Rate' => ['heartRate', null, null, 1000],
            'Points 1000 - Speed' => ['speed', null, null, 1000],
            'Points 1000 - Grade' => ['grade', null, null, 1000],
            'Points 1000 - Battery' => ['battery', null, null, 1000],
            'Points 1000 - Calories' => ['calories', null, null, 1000],

        ];
    }

}
