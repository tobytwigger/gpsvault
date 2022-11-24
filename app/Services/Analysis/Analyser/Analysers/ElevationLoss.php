<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class ElevationLoss extends AnalyserContract implements PointAnalyser
{
    private ?float $previousElevation = null;

    private ?float $elevationLoss = 0;

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getCumulativeElevationLoss() === null;
    }

    public function processPoint(Point $point): Point
    {
        if ($point->getElevation() !== null) {
            if ($this->previousElevation !== null) {
                if ($this->previousElevation > $point->getElevation()) {
                    $this->elevationLoss += $this->previousElevation - $point->getElevation();
                }
            }
            $this->previousElevation = $point->getElevation();
        }

        return $point;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getCumulativeElevationLoss() === null) {
            $analysis->setCumulativeElevationLoss($this->elevationLoss);
        }

        return $analysis;
    }
}
