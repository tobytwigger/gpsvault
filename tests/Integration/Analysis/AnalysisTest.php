<?php

namespace Tests\Integration\Analysis;

use App\Models\File;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use Tests\TestCase;

class AnalysisTest extends TestCase
{

    /** @test */
    public function it_analyses_a_file(){
        $file = File::factory()->dartmoorDevil()->create();

        /** @var Analysis $analysis */
        $analysis = Analyser::analyse($file);

        $this->assertEquals(7231, $analysis->getCalories());
        $this->assertEquals(65, $analysis->getAverageCadence());
        $this->assertEquals(166, $analysis->getAverageHeartrate());
        $this->assertEquals(0.27945935859905, $analysis->getAveragePace());
        $this->assertEquals(3.5783378485268, $analysis->getAverageSpeed());
        $this->assertEquals(9, $analysis->getAverageTemp());
        $this->assertEquals(143, $analysis->getAverageWatts());
        $this->assertEquals(2450, $analysis->getCumulativeElevationGain());
        $this->assertEquals(2480, $analysis->getCumulativeElevationLoss());
        $this->assertEquals(108573.927, round($analysis->getDistance(), 3));
        $this->assertEquals(30342.0, $analysis->getDuration());
        $this->assertEquals(50.594097, $analysis->getEndLatitude());
        $this->assertEquals(-3.67503, $analysis->getEndLongitude());
        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 07:59:52'), $analysis->getStartedAt());
        $this->assertEquals(\Carbon\Carbon::make('2021-10-31 16:25:34'), $analysis->getFinishedAt());
        $this->assertEquals(30254, $analysis->getKilojoules());
        $this->assertEquals(457, $analysis->getMaxAltitude());
        $this->assertEquals(28, $analysis->getMinAltitude());
        $this->assertEquals(207, $analysis->getMaxHeartrate());
        $this->assertEquals(16.88889, $analysis->getMaxSpeed());
        $this->assertEquals(25289, $analysis->getMovingTime());
        $this->assertEquals(50.594223, $analysis->getStartLatitude());
        $this->assertEquals(-3.674998, $analysis->getStartLongitude());
    }

    /** @test */
    public function it_analyses_a_fit_file(){
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_analyses_a_tcx_file(){
        $this->markTestIncomplete();
    }

}
