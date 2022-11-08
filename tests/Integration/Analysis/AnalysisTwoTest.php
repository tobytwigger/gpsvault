<?php

namespace Tests\Integration\Analysis;

class AnalysisTwoTest extends BaseAnalysisTestCase
{

    public function providesPointsToAnalyse(): array
    {
        return [
            'Start Latitude' => ['startLatitude', 52.02566],
            'Start Longitude' => ['startLongitude', -0.80121],
            'End Latitude' => ['endLatitude', 52.03618],
            'End Longitude' => ['endLongitude', -0.77922],
            'Average Pace' => ['averagePace', 0.43],
            'Distance' => ['distance', 10451.72],
            'Started At' => ['startedAt', \Carbon\Carbon::make('2022-05-24 15:02:41')],
            'Finished At' => ['finishedAt', \Carbon\Carbon::make('2022-05-24 15:09:15')],
            'Average Cadence' => ['averageCadence', 69.0],
            'Average Heartrate' => ['averageHeartrate', null],
            'Average Temp' => ['averageTemp', 18.0],
            'Max Altitude' => ['maxAltitude', 86.2],
            'Min Altitude' => ['minAltitude', 57.6],
            'Max Heartrate' => ['maxHeartrate', null],
            'Duration' => ['duration', 394.0],
            'Average Speed' => ['averageSpeed', 5.6480388014652485],
            'Moving Time' => ['movingTime', 4497.0],
            'Max Speed' => ['maxSpeed', 11.55],

//            'Calories' => ['calories', 147.0],
//            'Average Watts' => ['averageWatts', 147.0],
//            'Kilojoules' => ['kilojoules', 147.0],
//            'Average Watts' => ['averageWatts', 147.0],
//            'Cumulative Elevation Gain' => ['cumulativeELevationGain', 147.0],
//            'Cumulative Elevation Loss' => ['cumulativeELevationLoss', 147.0],
        ];
    }

    public function getFileName(): string
    {
        return 'analysis2.json';
    }
}
