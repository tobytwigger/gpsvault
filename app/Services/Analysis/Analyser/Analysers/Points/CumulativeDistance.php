<?php

namespace App\Services\Analysis\Analyser\Analysers\Points;

use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\PointAnalyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Location\Coordinate;
use Location\Distance\Vincenty;

class CumulativeDistance extends AnalyserContract implements PointAnalyser
{
    private ?Point $previousPoint = null;

    private ?float $cumulativeDistance = 0.0;

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
        if ($point->getCumulativeDistance() === null && $point->getLatitude() !== null && $point->getLongitude() !== null) {
            if ($this->previousPoint !== null) {
                $coordinate1 = new Coordinate($this->previousPoint->getLatitude(), $this->previousPoint->getLongitude());
                $coordinate2 = new Coordinate($point->getLatitude(), $point->getLongitude());
                $calculator = new Vincenty();
                $this->cumulativeDistance += $calculator->getDistance($coordinate1, $coordinate2);
            }
            $point->setCumulativeDistance($this->cumulativeDistance);
            $this->previousPoint = $point;
        }

        return $point;
    }
}
