<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class MaxAltitude extends AnalyserContract implements PointAnalyser
{
    private ?float $maxAltitude = null;

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getMaxAltitude() === null;
    }

    public function processPoint(Point $point): Point
    {
        if ($point->getElevation() !== null) {
            if ($this->maxAltitude === null || $this->maxAltitude < $point->getElevation()) {
                $this->maxAltitude = $point->getElevation();
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
        if ($analysis->getMaxAltitude() === null) {
            $analysis->setMaxAltitude($this->maxAltitude);
        }

        return $analysis;
    }
}
