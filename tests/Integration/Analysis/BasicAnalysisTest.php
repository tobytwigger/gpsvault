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

    public function providesPointsToAnalyse(): array
    {
        return [
            'Start Latitude' => [$this->createAnalysis('analysis1.json'), 'startLatitude', 52.055603],
            'Start Longitude' => [$this->createAnalysis('analysis1.json'), 'startLongitude', -0.6996],
            'End Latitude' => [$this->createAnalysis('analysis1.json'), 'endLatitude', 52.025595],
            'End Longitude' => [$this->createAnalysis('analysis1.json'), 'endLongitude', -0.80106],
//            'Average Pace' => [$this->createAnalysis('analysis1.json'), 'averagePace', -0.80106],
//            'Distance' => [$this->createAnalysis('analysis1.json'), 'distance', -0.80106],
//            'Duration' => [$this->createAnalysis('analysis1.json'), 'duration', -0.80106],
//            'Started At' => [$this->createAnalysis('analysis1.json'), 'startedAt', -0.80106],
//            'Finished At' => [$this->createAnalysis('analysis1.json'), 'finishedAt', -0.80106],
//            'Average Cadence' => [$this->createAnalysis('analysis1.json'), 'averageCadence', -0.80106],
//            'Average Heartrate' => [$this->createAnalysis('analysis1.json'), 'averageHeartrate', -0.80106],
//            'Average Temp' => [$this->createAnalysis('analysis1.json'), 'averageTemp', -0.80106],
//            'Max Altitude' => [$this->createAnalysis('analysis1.json'), 'maxAltitude', -0.80106],
//            'Min Altitude' => [$this->createAnalysis('analysis1.json'), 'minAltitude', -0.80106],
//            'Max Heartrate' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Calories' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Moving Time' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Max Speed' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Average Watts' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Kilojoules' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Average Speed' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Cumulative Elevation Gain' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Cumulative Elevation Loss' => [$this->createAnalysis('analysis2.json'), 'maxHeartrate', 147.0],
//            'Duration' => [$this->createAnalysis('analysis2.json'), 'duration', ],


//            'Points 1 - Latitude' => [$this->createAnalysis('analysis1.json'), 'latitude', -0.80106, 0],
//            'Points 1 - Longitude' => [$this->createAnalysis('analysis1.json'), 'longitude', -0.80106, 0],
//            'Points 1 - Elevation' => [$this->createAnalysis('analysis1.json'), 'elevation', -0.80106, 0],
//            'Points 1 - Time' => [$this->createAnalysis('analysis1.json'), 'time', -0.80106, 0],
//            'Points 1 - Cadence' => [$this->createAnalysis('analysis1.json'), 'cadence', -0.80106, 0],
//            'Points 1 - Temperature' => [$this->createAnalysis('analysis1.json'), 'temperature', -0.80106, 0],
//            'Points 1 - Heart Rate' => [$this->createAnalysis('analysis1.json'), 'heartRate', -0.80106, 0],
//            'Points 1 - Speed' => [$this->createAnalysis('analysis1.json'), 'speed', -0.80106, 0],
//            'Points 1 - Cumulative Distance' => [$this->createAnalysis('analysis1.json'), 'cumulativeDistance', -0.80106, 0],
//            'Points 1 - Grade' => [$this->createAnalysis('analysis1.json'), 'grade', -0.80106, 0],
//            'Points 1 - Battery' => [$this->createAnalysis('analysis1.json'), 'battery', -0.80106, 0],
//            'Points 1 - Calories' => [$this->createAnalysis('analysis1.json'), 'calories', -0.80106, 0],

//            'Points 100 - Latitude' => [$this->createAnalysis('analysis1.json'), 'latitude', -0.80106, 100],
//            'Points 100 - Longitude' => [$this->createAnalysis('analysis1.json'), 'longitude', -0.80106, 100],
//            'Points 100 - Elevation' => [$this->createAnalysis('analysis1.json'), 'elevation', -0.80106, 100],
//            'Points 100 - Time' => [$this->createAnalysis('analysis1.json'), 'time', -0.80106, 100],
//            'Points 100 - Cadence' => [$this->createAnalysis('analysis1.json'), 'cadence', -0.80106, 100],
//            'Points 100 - Temperature' => [$this->createAnalysis('analysis1.json'), 'temperature', -0.80106, 100],
//            'Points 100 - Heart Rate' => [$this->createAnalysis('analysis1.json'), 'heartRate', -0.80106, 100],
//            'Points 100 - Speed' => [$this->createAnalysis('analysis1.json'), 'speed', -0.80106, 100],
//            'Points 100 - Cumulative Distance' => [$this->createAnalysis('analysis1.json'), 'cumulativeDistance', -0.80106, 100],
//            'Points 100 - Grade' => [$this->createAnalysis('analysis1.json'), 'grade', -0.80106, 100],
//            'Points 100 - Battery' => [$this->createAnalysis('analysis1.json'), 'battery', -0.80106, 100],
//            'Points 100 - Calories' => [$this->createAnalysis('analysis1.json'), 'calories', -0.80106, 100],


//            'Points 1000 - Latitude' => [$this->createAnalysis('analysis1.json'), 'latitude', -0.80106, 1000],
//            'Points 1000 - Longitude' => [$this->createAnalysis('analysis1.json'), 'longitude', -0.80106, 1000],
//            'Points 1000 - Elevation' => [$this->createAnalysis('analysis1.json'), 'elevation', -0.80106, 1000],
//            'Points 1000 - Time' => [$this->createAnalysis('analysis1.json'), 'time', -0.80106, 1000],
//            'Points 1000 - Cadence' => [$this->createAnalysis('analysis1.json'), 'cadence', -0.80106, 1000],
//            'Points 1000 - Temperature' => [$this->createAnalysis('analysis1.json'), 'temperature', -0.80106, 1000],
//            'Points 1000 - Heart Rate' => [$this->createAnalysis('analysis1.json'), 'heartRate', -0.80106, 1000],
//            'Points 1000 - Speed' => [$this->createAnalysis('analysis1.json'), 'speed', -0.80106, 1000],
//            'Points 1000 - Cumulative Distance' => [$this->createAnalysis('analysis1.json'), 'cumulativeDistance', -0.80106, 1000],
//            'Points 1000 - Grade' => [$this->createAnalysis('analysis1.json'), 'grade', -0.80106, 1000],
//            'Points 1000 - Battery' => [$this->createAnalysis('analysis1.json'), 'battery', -0.80106, 1000],
//            'Points 1000 - Calories' => [$this->createAnalysis('analysis1.json'), 'calories', -0.80106, 1000],

        ];
    }

}
