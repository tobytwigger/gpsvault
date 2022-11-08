<?php

namespace Tests\Integration\Analysis;

class AnalysisTwoTest extends BaseAnalysisTestCase
{

    /** @test */
    public function todo_finish_commented_points(){
        $this->markTestIncomplete();
    }

    public function providesPointsToAnalyse(): array
    {
        return [
            'Start Latitude' => ['startLatitude', 52.02566],
            'Start Longitude' => ['startLongitude', -0.80121],
            'End Latitude' => ['endLatitude', 52.03618],
            'End Longitude' => ['endLongitude', -0.77922],
            'Average Pace' => ['averagePace', 0.04],
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

            // TODO Finish these
//            'Calories' => ['maxHeartrate', 147.0],
//            'Moving Time' => ['maxHeartrate', 147.0],
//            'Max Speed' => ['maxHeartrate', 147.0],
//            'Average Watts' => ['maxHeartrate', 147.0],
//            'Kilojoules' => ['maxHeartrate', 147.0],
//            'Average Watts' => ['maxHeartrate', 147.0],
//            'Average Speed' => ['maxHeartrate', 147.0],
//            'Cumulative Elevation Gain' => ['maxHeartrate', 147.0],
//            'Cumulative Elevation Loss' => ['maxHeartrate', 147.0],
        ];
    }

    public function getFileName(): string
    {
        return 'analysis2.json';
    }
}
