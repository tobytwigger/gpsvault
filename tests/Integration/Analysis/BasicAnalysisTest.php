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
            'Start Latitude' => [$this->createAnalysis('tracks1.geojson'), 'startLatitude', 52.055603],
            'Start Longitude' => [$this->createAnalysis('tracks1.geojson'), 'startLongitude', -0.6996],
            'End Latitude' => [$this->createAnalysis('tracks1.geojson'), 'endLatitude', 52.025595],
            'End Longitude' => [$this->createAnalysis('tracks1.geojson'), 'endLongitude', -0.80106],
//            'Average Pace' => [$this->createAnalysis('tracks1.geojson'), 'averagePace', -0.80106],
//            'Distance' => [$this->createAnalysis('tracks1.geojson'), 'distance', -0.80106],
//            'Duration' => [$this->createAnalysis('tracks1.geojson'), 'duration', -0.80106],
//            'Started At' => [$this->createAnalysis('tracks1.geojson'), 'startedAt', -0.80106],
//            'Finished At' => [$this->createAnalysis('tracks1.geojson'), 'finishedAt', -0.80106],
//            'Average Cadence' => [$this->createAnalysis('tracks1.geojson'), 'averageCadence', -0.80106],
//            'Average Heartrate' => [$this->createAnalysis('tracks1.geojson'), 'averageHeartrate', -0.80106],
//            'Average Temp' => [$this->createAnalysis('tracks1.geojson'), 'averageTemp', -0.80106],
//            'Max Altitude' => [$this->createAnalysis('tracks1.geojson'), 'maxAltitude', -0.80106],
//            'MinAltitude' => [$this->createAnalysis('tracks1.geojson'), 'minAltitude', -0.80106],

//            'Points 1 - Latitude' => [$this->createAnalysis('tracks1.geojson'), 'latitude', -0.80106, 0],
//            'Points 1 - Longitude' => [$this->createAnalysis('tracks1.geojson'), 'longitude', -0.80106, 0],
//            'Points 1 - Elevation' => [$this->createAnalysis('tracks1.geojson'), 'elevation', -0.80106, 0],
//            'Points 1 - Time' => [$this->createAnalysis('tracks1.geojson'), 'time', -0.80106, 0],
//            'Points 1 - Cadence' => [$this->createAnalysis('tracks1.geojson'), 'cadence', -0.80106, 0],
//            'Points 1 - Temperature' => [$this->createAnalysis('tracks1.geojson'), 'temperature', -0.80106, 0],
//            'Points 1 - Heart Rate' => [$this->createAnalysis('tracks1.geojson'), 'heartRate', -0.80106, 0],
//            'Points 1 - Speed' => [$this->createAnalysis('tracks1.geojson'), 'speed', -0.80106, 0],
//            'Points 1 - Cumulative Distance' => [$this->createAnalysis('tracks1.geojson'), 'cumulativeDistance', -0.80106, 0],
//            'Points 1 - Grade' => [$this->createAnalysis('tracks1.geojson'), 'grade', -0.80106, 0],
//            'Points 1 - Battery' => [$this->createAnalysis('tracks1.geojson'), 'battery', -0.80106, 0],
//            'Points 1 - Calories' => [$this->createAnalysis('tracks1.geojson'), 'calories', -0.80106, 0],

//            'Points 100 - Latitude' => [$this->createAnalysis('tracks1.geojson'), 'latitude', -0.80106, 100],
//            'Points 100 - Longitude' => [$this->createAnalysis('tracks1.geojson'), 'longitude', -0.80106, 100],
//            'Points 100 - Elevation' => [$this->createAnalysis('tracks1.geojson'), 'elevation', -0.80106, 100],
//            'Points 100 - Time' => [$this->createAnalysis('tracks1.geojson'), 'time', -0.80106, 100],
//            'Points 100 - Cadence' => [$this->createAnalysis('tracks1.geojson'), 'cadence', -0.80106, 100],
//            'Points 100 - Temperature' => [$this->createAnalysis('tracks1.geojson'), 'temperature', -0.80106, 100],
//            'Points 100 - Heart Rate' => [$this->createAnalysis('tracks1.geojson'), 'heartRate', -0.80106, 100],
//            'Points 100 - Speed' => [$this->createAnalysis('tracks1.geojson'), 'speed', -0.80106, 100],
//            'Points 100 - Cumulative Distance' => [$this->createAnalysis('tracks1.geojson'), 'cumulativeDistance', -0.80106, 100],
//            'Points 100 - Grade' => [$this->createAnalysis('tracks1.geojson'), 'grade', -0.80106, 100],
//            'Points 100 - Battery' => [$this->createAnalysis('tracks1.geojson'), 'battery', -0.80106, 100],
//            'Points 100 - Calories' => [$this->createAnalysis('tracks1.geojson'), 'calories', -0.80106, 100],


//            'Points 1000 - Latitude' => [$this->createAnalysis('tracks1.geojson'), 'latitude', -0.80106, 1000],
//            'Points 1000 - Longitude' => [$this->createAnalysis('tracks1.geojson'), 'longitude', -0.80106, 1000],
//            'Points 1000 - Elevation' => [$this->createAnalysis('tracks1.geojson'), 'elevation', -0.80106, 1000],
//            'Points 1000 - Time' => [$this->createAnalysis('tracks1.geojson'), 'time', -0.80106, 1000],
//            'Points 1000 - Cadence' => [$this->createAnalysis('tracks1.geojson'), 'cadence', -0.80106, 1000],
//            'Points 1000 - Temperature' => [$this->createAnalysis('tracks1.geojson'), 'temperature', -0.80106, 1000],
//            'Points 1000 - Heart Rate' => [$this->createAnalysis('tracks1.geojson'), 'heartRate', -0.80106, 1000],
//            'Points 1000 - Speed' => [$this->createAnalysis('tracks1.geojson'), 'speed', -0.80106, 1000],
//            'Points 1000 - Cumulative Distance' => [$this->createAnalysis('tracks1.geojson'), 'cumulativeDistance', -0.80106, 1000],
//            'Points 1000 - Grade' => [$this->createAnalysis('tracks1.geojson'), 'grade', -0.80106, 1000],
//            'Points 1000 - Battery' => [$this->createAnalysis('tracks1.geojson'), 'battery', -0.80106, 1000],
//            'Points 1000 - Calories' => [$this->createAnalysis('tracks1.geojson'), 'calories', -0.80106, 1000],

        ];
    }

}
