<?php

namespace App\Services\Analysis\Analyser\Analysers\Points;

use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\PointAnalyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;

class Speed extends AnalyserContract implements PointAnalyser
{
    private ?float $previousCumulativeDistance = 0.0;

    private ?Carbon $previousTime = null;

    public function canRun(Analysis $analysis): bool
    {
        return false;
    }

    protected function run(Analysis $analysis): Analysis
    {
        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        if ($point->getSpeed() === null && $point->getCumulativeDistance() !== null && $point->getTime() !== null) {
            $speed = 0.0;
            if ($this->previousTime !== null && $this->previousCumulativeDistance !== null) {
                $segmentDistance = $point->getCumulativeDistance() - $this->previousCumulativeDistance;
                $segmentTime = $point->getTime()->diffInSeconds($this->previousTime);
                if ($segmentTime > 0.0) {
                    $speed = $segmentDistance / $segmentTime;
                }
            }
            $point->setSpeed($speed);
            $this->previousTime = $point->getTime();
            $this->previousCumulativeDistance = $point->getCumulativeDistance();
        }

        return $point;
    }
}
