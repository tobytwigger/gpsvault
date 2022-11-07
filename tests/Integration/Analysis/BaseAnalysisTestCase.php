<?php

namespace Tests\Integration\Analysis;

use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class BaseAnalysisTestCase extends TestCase
{

    abstract public function providesPointsToAnalyse(): array;

    protected function createAnalysis(string $filename): Analysis
    {
        $analysisFile = json_decode(file_get_contents(__DIR__ . '/../../assets/analysis/' . $filename), true);
        $analysis = new Analysis();

        if (array_key_exists('points', $analysisFile)) {
            foreach ($analysisFile['points'] as $point) {
                $analysis->pushPoint(
                    (new Point())
                        ->setLatitude(data_get($point, 'coordinates.1'))
                        ->setLongitude(data_get($point, 'coordinates.0'))
                        ->setElevation($point['elevation'] ?? null)
                        ->setTime(array_key_exists('time', $point) ? Carbon::make($point['time']) : null)
                        ->setCadence($point['cadence'] ?? null)
                        ->setTemperature($point['temperature'] ?? null)
                        ->setHeartRate($point['heart_rate'] ?? null)
                        ->setSpeed($point['speed'] ?? null)
                        ->setGrade($point['grade'] ?? null)
                        ->setBattery($point['battery'] ?? null)
                        ->setCalories($point['calories'] ?? null)
                        ->setCumulativeDistance($point['cumulative_distance'] ?? null)
                );
            }
        }

        return $analysis;
    }

    /**
     * @test
     * @dataProvider providesPointsToAnalyse
     */
    public function it_analyses_a_set_of_points_correctly(Analysis $analysis, string $field, mixed $value, ?int $pointsIndex = null, mixed $initialValue = null){
        $methodName = 'get' . Str::ucfirst($field);

        if($pointsIndex === null) {
            $this->assertEquals($initialValue, $analysis->$methodName());

            $analysed = Analyser::runAnalysis($analysis);

            $this->assertEquals($value, $analysed->$methodName());
        } else {
            $this->assertIsArray($analysis->getPoints());
            $this->assertEquals($initialValue, $analysis->getPoints()[$pointsIndex]->$methodName());

            $analysed = Analyser::runAnalysis($analysis);

            $this->assertEquals($initialValue, $analysis->getPoints()[$pointsIndex]->$methodName());

            $this->assertEquals($value, $analysed->getPoints()[$pointsIndex]->$methodName());
        }

    }

}
