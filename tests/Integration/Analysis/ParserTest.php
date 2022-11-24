<?php

namespace Tests\Integration\Analysis;

use App\Models\File;
use App\Services\Analysis\Parser\Parser;
use Tests\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function it_analyses_a_gpx_file()
    {
        $file = File::factory()->simpleGpx()->create();

        $analysis = Parser::parse($file);

        $this->assertEquals(null, $analysis->getStartLatitude());
        $this->assertEquals(null, $analysis->getStartLongitude());
        $this->assertEquals(null, $analysis->getEndLatitude());
        $this->assertEquals(null, $analysis->getEndLongitude());
        $this->assertEquals(null, $analysis->getAveragePace());
        $this->assertEquals(null, $analysis->getDistance());
        $this->assertEquals(null, $analysis->getDuration());
        $this->assertEquals(null, $analysis->getStartedAt());
        $this->assertEquals(null, $analysis->getFinishedAt());
        $this->assertEquals(null, $analysis->getAverageCadence());
        $this->assertEquals(null, $analysis->getAverageHeartrate());
        $this->assertEquals(null, $analysis->getMaxHeartrate());
        $this->assertEquals(null, $analysis->getAverageTemp());
        $this->assertEquals(null, $analysis->getMaxAltitude());
        $this->assertEquals(null, $analysis->getMinAltitude());

        $this->assertIsArray($analysis->getPoints());
        $this->assertCount(2, $analysis->getPoints());

        $this->assertEquals(50.5942230, $analysis->getPoints()[0]->getLatitude());
        $this->assertEquals(-3.6749980, $analysis->getPoints()[0]->getLongitude());
        $this->assertEquals(46.6, $analysis->getPoints()[0]->getElevation());
        $this->assertEquals('2021-10-31 07:59:52', $analysis->getPoints()[0]->getTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(65, $analysis->getPoints()[0]->getCadence());
        $this->assertEquals(9, $analysis->getPoints()[0]->getTemperature());
        $this->assertEquals(150, $analysis->getPoints()[0]->getHeartRate());
        $this->assertNull($analysis->getPoints()[0]->getSpeed());
        $this->assertNull($analysis->getPoints()[0]->getCumulativeDistance());
        $this->assertNull($analysis->getPoints()[0]->getGrade());
        $this->assertNull($analysis->getPoints()[0]->getBattery());
        $this->assertNull($analysis->getPoints()[0]->getCalories());

        $this->assertEquals(50.5942231, $analysis->getPoints()[1]->getLatitude());
        $this->assertEquals(-3.6749981, $analysis->getPoints()[1]->getLongitude());
        $this->assertEquals(46.7, $analysis->getPoints()[1]->getElevation());
        $this->assertEquals('2021-10-31 07:59:53', $analysis->getPoints()[1]->getTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(66, $analysis->getPoints()[1]->getCadence());
        $this->assertEquals(10, $analysis->getPoints()[1]->getTemperature());
        $this->assertEquals(151, $analysis->getPoints()[1]->getHeartRate());
        $this->assertNull($analysis->getPoints()[1]->getSpeed());
        $this->assertNull($analysis->getPoints()[1]->getCumulativeDistance());
        $this->assertNull($analysis->getPoints()[1]->getGrade());
        $this->assertNull($analysis->getPoints()[1]->getBattery());
        $this->assertNull($analysis->getPoints()[1]->getCalories());
    }

    /** @test */
    public function todo_it_analyses_a_fit_file()
    {
        $file = File::factory()->simpleFit()->create();

        $analysis = Parser::parse($file);

        $this->assertEquals(null, $analysis->getStartLatitude());
        $this->assertEquals(null, $analysis->getStartLongitude());
        $this->assertEquals(null, $analysis->getEndLatitude());
        $this->assertEquals(null, $analysis->getEndLongitude());
        $this->assertEquals(null, $analysis->getAveragePace());
        $this->assertEquals(null, $analysis->getDistance());
        $this->assertEquals(null, $analysis->getDuration());
        $this->assertEquals(null, $analysis->getStartedAt());
        $this->assertEquals(null, $analysis->getFinishedAt());
        $this->assertEquals(null, $analysis->getAverageCadence());
        $this->assertEquals(null, $analysis->getAverageHeartrate());
        $this->assertEquals(null, $analysis->getMaxHeartrate());
        $this->assertEquals(null, $analysis->getAverageTemp());
        $this->assertEquals(null, $analysis->getMaxAltitude());
        $this->assertEquals(null, $analysis->getMinAltitude());

        $this->assertIsArray($analysis->getPoints());
        $this->assertCount(2, $analysis->getPoints());

        $this->assertEquals(50.59422, $analysis->getPoints()[0]->getLatitude());
        $this->assertEquals(-3.675, $analysis->getPoints()[0]->getLongitude());
        $this->assertEquals(46.6, round($analysis->getPoints()[0]->getElevation(), 1));
        $this->assertEquals('2021-10-31 07:59:52', $analysis->getPoints()[0]->getTime()->format('Y-m-d H:i:s'));
        $this->assertNull($analysis->getPoints()[0]->getCadence());
        $this->assertNull($analysis->getPoints()[0]->getTemperature());
        $this->assertNull($analysis->getPoints()[0]->getHeartRate());
        $this->assertNull($analysis->getPoints()[0]->getSpeed());
        $this->assertNull($analysis->getPoints()[0]->getCumulativeDistance());
        $this->assertNull($analysis->getPoints()[0]->getGrade());
        $this->assertNull($analysis->getPoints()[0]->getBattery());
        $this->assertNull($analysis->getPoints()[0]->getCalories());

        $this->assertEquals(50.59422, $analysis->getPoints()[1]->getLatitude());
        $this->assertEquals(-3.675, $analysis->getPoints()[1]->getLongitude());
        $this->assertEquals(46.8, round($analysis->getPoints()[1]->getElevation(), 1));
        $this->assertEquals('2021-10-31 07:59:53', $analysis->getPoints()[1]->getTime()->format('Y-m-d H:i:s'));
        $this->assertNull($analysis->getPoints()[1]->getCadence());
        $this->assertNull($analysis->getPoints()[1]->getTemperature());
        $this->assertNull($analysis->getPoints()[1]->getHeartRate());
        $this->assertNull($analysis->getPoints()[1]->getSpeed());
        $this->assertNull($analysis->getPoints()[1]->getCumulativeDistance());
        $this->assertNull($analysis->getPoints()[1]->getGrade());
        $this->assertNull($analysis->getPoints()[1]->getBattery());
        $this->assertNull($analysis->getPoints()[1]->getCalories());
    }

    /** @test */
    public function todo_it_analyses_a_tcx_file()
    {
        $file = File::factory()->simpleTcx()->create();

        $analysis = Parser::parse($file);

        $this->assertEquals(null, $analysis->getStartLatitude());
        $this->assertEquals(null, $analysis->getStartLongitude());
        $this->assertEquals(null, $analysis->getEndLatitude());
        $this->assertEquals(null, $analysis->getEndLongitude());
        $this->assertEquals(null, $analysis->getAveragePace());
        $this->assertEquals(null, $analysis->getDistance());
        $this->assertEquals(null, $analysis->getDuration());
        $this->assertEquals(null, $analysis->getStartedAt());
        $this->assertEquals(null, $analysis->getFinishedAt());
        $this->assertEquals(null, $analysis->getAverageCadence());
        $this->assertEquals(null, $analysis->getAverageHeartrate());
        $this->assertEquals(null, $analysis->getMaxHeartrate());
        $this->assertEquals(null, $analysis->getAverageTemp());
        $this->assertEquals(null, $analysis->getMaxAltitude());
        $this->assertEquals(null, $analysis->getMinAltitude());

        $this->assertIsArray($analysis->getPoints());
        $this->assertCount(2, $analysis->getPoints());

        $this->assertEquals(50.5942230, $analysis->getPoints()[0]->getLatitude());
        $this->assertEquals(-3.6749980, $analysis->getPoints()[0]->getLongitude());
        $this->assertEquals(46.6, $analysis->getPoints()[0]->getElevation());
        $this->assertEquals('2021-10-31 07:59:52', $analysis->getPoints()[0]->getTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(65, $analysis->getPoints()[0]->getCadence());
        $this->assertNull($analysis->getPoints()[0]->getTemperature());
        $this->assertEquals(150, $analysis->getPoints()[0]->getHeartRate());
        $this->assertNull($analysis->getPoints()[0]->getSpeed());
        $this->assertNull($analysis->getPoints()[0]->getCumulativeDistance());
        $this->assertNull($analysis->getPoints()[0]->getGrade());
        $this->assertNull($analysis->getPoints()[0]->getBattery());
        $this->assertNull($analysis->getPoints()[0]->getCalories());

        $this->assertEquals(50.5942231, $analysis->getPoints()[1]->getLatitude());
        $this->assertEquals(-3.6749981, $analysis->getPoints()[1]->getLongitude());
        $this->assertEquals(46.7, $analysis->getPoints()[1]->getElevation());
        $this->assertEquals('2021-10-31 07:59:53', $analysis->getPoints()[1]->getTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(66, $analysis->getPoints()[1]->getCadence());
        $this->assertNull($analysis->getPoints()[1]->getTemperature());
        $this->assertEquals(151, $analysis->getPoints()[1]->getHeartRate());
        $this->assertNull($analysis->getPoints()[1]->getSpeed());
        $this->assertNull($analysis->getPoints()[1]->getCumulativeDistance());
        $this->assertNull($analysis->getPoints()[1]->getGrade());
        $this->assertNull($analysis->getPoints()[1]->getBattery());
        $this->assertNull($analysis->getPoints()[1]->getCalories());
    }
}
