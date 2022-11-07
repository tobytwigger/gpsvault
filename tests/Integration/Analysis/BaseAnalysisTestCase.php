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
        $points = [];

        foreach(json_decode(file_get_contents(__DIR__ . '/../../assets/analysis/' . $filename), true)['features'] as $coordinate) {
            $points[] = (new Point())
                ->setLatitude($coordinate['geometry']['coordinates'][1])
                ->setLongitude($coordinate['geometry']['coordinates'][0])
                ->setTime(Carbon::parse($coordinate['properties']['time']));
        }

        return (new Analysis())
            ->setPoints($points);
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
