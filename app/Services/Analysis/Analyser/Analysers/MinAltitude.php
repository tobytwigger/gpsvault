<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class MinAltitude extends AnalyserContract implements PointAnalyser
{
    private ?float $minAltitude = null;

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getMinAltitude() === null;
    }

    public function processPoint(Point $point): Point
    {
        if ($point->getElevation() !== null) {
            if ($this->minAltitude === null || $this->minAltitude > $point->getElevation()) {
                $this->minAltitude = $point->getElevation();
            }
        }
        return $point;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getMinAltitude() === null) {
            $analysis->setMinAltitude($this->minAltitude);
        }

        return $analysis;
    }
}
