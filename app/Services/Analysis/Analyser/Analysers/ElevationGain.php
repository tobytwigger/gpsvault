<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class ElevationGain extends AnalyserContract implements PointAnalyser
{
    private ?float $previousElevation = null;

    private ?float $elevationGain = 0;

    public function processPoint(Point $point): Point
    {
        if ($point->getElevation() !== null) {
            if ($this->previousElevation !== null) {
                if ($this->previousElevation < $point->getElevation()) {
                    $this->elevationGain += $point->getElevation() - $this->previousElevation;
                }
            }
            $this->previousElevation = $point->getElevation();
        }
        return $point;
    }

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getCumulativeElevationGain() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getCumulativeElevationGain() === null) {
            $analysis->setCumulativeElevationGain($this->elevationGain);
        }

        return $analysis;
    }
}
