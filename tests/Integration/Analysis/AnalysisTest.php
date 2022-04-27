<?php

namespace Tests\Integration\Analysis;

use App\Models\File;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use Tests\TestCase;

class AnalysisTest extends TestCase
{

    /** @test */
    public function it_analyses_a_gpx_file(){
        $this->markTestIncomplete();

        $file = File::factory()->dartmoorDevilGpx()->create();

        /** @var Analysis $analysis */
        $analysis = Analyser::analyse($file);

        $this->assertEquals(50.594223, $analysis->getStartLatitude());
        $this->assertEquals(-3.674998, $analysis->getStartLongitude());
        $this->assertEquals(50.594097, $analysis->getEndLatitude());
        $this->assertEquals(-3.67503, $analysis->getEndLongitude());
        $this->assertEquals(0.28, $analysis->getAveragePace());
        $this->assertEquals(108573.93, $analysis->getDistance());
        $this->assertEquals(30342.0, $analysis->getDuration());
        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 07:59:52'), $analysis->getStartedAt());
        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 16:25:34'), $analysis->getFinishedAt());
        $this->assertEquals(64.0, $analysis->getAverageCadence(), 2);
        $this->assertEquals(166.0, $analysis->getAverageHeartrate(), 2);
        $this->assertEquals(207.0, $analysis->getMaxHeartrate(), 2);
        $this->assertEquals(9.0, $analysis->getAverageTemp());
        $this->assertEquals(459.2, $analysis->getMaxAltitude());
        $this->assertEquals(26.0, $analysis->getMinAltitude());

//        $this->assertEquals(25289, $analysis->getMovingTime());
//        $this->assertEquals(2450, $analysis->getCumulativeElevationGain());
//        $this->assertEquals(2480, $analysis->getCumulativeElevationLoss());

        // Not working because we don't take into account stationary time
//        $this->assertEquals(4.25, $analysis->getAverageSpeed());
        // TODO Make sure file has a speed then test this again
//        $this->assertEquals(16.89, $analysis->getMaxSpeed());

//        $this->assertEquals(143, $analysis->getAverageWatts());
//        $this->assertEquals(7231, $analysis->getCalories());
//        $this->assertEquals(30254, $analysis->getKilojoules());
    }

    /** @test */
    public function it_analyses_a_fit_file(){
        $this->markTestIncomplete();

        $file = File::factory()->dartmoorDevilFit()->create();

        /** @var Analysis $analysis */
        $analysis = Analyser::analyse($file);

        $this->assertEquals(50.59422, $analysis->getStartLatitude());
        $this->assertEquals(-3.675, $analysis->getStartLongitude());
        $this->assertEquals(50.5941, $analysis->getEndLatitude());
        $this->assertEquals(-3.67503, $analysis->getEndLongitude());
        $this->assertEquals(0.28, $analysis->getAveragePace());
        $this->assertEquals(109147.68, $analysis->getDistance());
        $this->assertEquals(30344.0, $analysis->getDuration());

        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 07:59:51'), $analysis->getStartedAt());
        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 16:25:34'), $analysis->getFinishedAt());
        $this->assertEquals(64.0, $analysis->getAverageCadence(), 2);
        $this->assertEquals(166.0, $analysis->getAverageHeartrate(), 2);
        $this->assertEquals(207.0, $analysis->getMaxHeartrate(), 2);
        $this->assertEquals(459.2, $analysis->getMaxAltitude());
        $this->assertEquals(26.0, $analysis->getMinAltitude());
        $this->assertEquals(16.8, $analysis->getMaxSpeed());
        $this->assertEquals(9.0, $analysis->getAverageTemp());

        $this->assertEquals(25533.0, $analysis->getMovingTime());
//        $this->assertEquals(2450, $analysis->getCumulativeElevationGain());
//        $this->assertEquals(2480, $analysis->getCumulativeElevationLoss());

        // Not working because we don't take into account stationary time
//        $this->assertEquals(4.25, $analysis->getAverageSpeed());
//        $this->assertEquals(143, $analysis->getAverageWatts());
//        $this->assertEquals(7231, $analysis->getCalories());
//        $this->assertEquals(30254, $analysis->getKilojoules());
    }

    /** @test */
    public function it_analyses_a_tcx_file(){
        $this->markTestIncomplete();
    }

}
