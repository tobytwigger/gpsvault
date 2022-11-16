<?php

namespace Tests\Unit\Services\Analyser;

use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Tests\TestCase;

class AnalyserTest extends TestCase
{
    /** @test */
    public function todo_scaffolding_test_this_package()
    {
        $this->markTestSkipped();
    }

    /** @test */
    public function it_analyses_a_set_of_points()
    {
        $this->markTestIncomplete();
        
        $emptyAnalysis = $this->getAnalysisFrom(base_path('/tests/assets/points/example1.json'));

        $analysis = Analyser::runAnalysis($emptyAnalysis);

        $this->assertEquals(52.02566, $analysis->getStartLatitude());
        $this->assertEquals(-0.80121, $analysis->getStartLongitude());
        $this->assertEquals(52.03618, $analysis->getEndLatitude());
        $this->assertEquals(-0.77922, $analysis->getEndLongitude());
        $this->assertEquals(0.27, $analysis->getAveragePace());
        $this->assertEquals(38290, $analysis->getDistance());
        $this->assertEquals(5310.0, $analysis->getDuration());
        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 07:59:52'), $analysis->getStartedAt());
        $this->assertEquals(\Carbon\Carbon::make('2022-05-24 15:02:41'), $analysis->getFinishedAt());
        $this->assertEquals(76.0, $analysis->getAverageCadence(), 2);
        $this->assertEquals(147.0, $analysis->getAverageHeartrate(), 2);
        $this->assertEquals(179.0, $analysis->getMaxHeartrate(), 2);
        $this->assertEquals(8, $analysis->getAverageTemp());
        $this->assertEquals(100.0, $analysis->getMaxAltitude());
        $this->assertEquals(26.0, $analysis->getMinAltitude());
    }

    private function getAnalysisFrom(string $filename): Analysis
    {
        $analysisFile = json_decode(file_get_contents($filename), true);
        $analysis = new Analysis();

        if (array_key_exists('points', $analysisFile)) {
            foreach ($analysisFile['points'] as $point) {
                $analysis->pushPoint(
                    (new Point())
                        ->setLatitude(data_get($point, 'points.coordinates.1'))
                        ->setLongitude(data_get($point, 'points.coordinates.0'))
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
}
