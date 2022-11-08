<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\PointAnalyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Location\Coordinate;
use Location\Distance\Vincenty;

class MovingTime extends AnalyserContract implements PointAnalyser
{

    private ?float $previousCumulativeDistance = 0.0;

    private ?Carbon $previousTime = null;

    private float $movingTime = 0.0;

    /**
     * How many meters off a point can be to be considered stopped
     *
     * @var float
     */
    private float $tolerance = 0.25;

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getMovingTime() === null;
    }

    protected function run(Analysis $analysis): Analysis
    {
        $analysis->setMovingTime($this->movingTime);
        return $analysis;
    }

    public function processPoint(Point $point): Point
    {

        if($point->getCumulativeDistance() !== null && $point->getTime() !== null) {
            if($point->getCumulativeDistance() - $this->tolerance > $this->previousCumulativeDistance
            || $point->getCumulativeDistance() + $this->tolerance < $this->previousCumulativeDistance) {
                $timeDiff = $point->getTime()->diffInSeconds($this->previousTime);
                $this->movingTime += $timeDiff;
            }
            $this->previousTime = $point->getTime();
            $this->previousCumulativeDistance = $point->getCumulativeDistance();
        }
        return $point;
    }
}
