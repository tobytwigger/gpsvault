<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class MaxSpeed extends AnalyserContract implements PointAnalyser
{
    private ?float $maxSpeed = null;

    public function canRun(Analysis $analysis): bool
    {
        return $this->maxSpeed !== null && $analysis->getMaxSpeed() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($this->maxSpeed) {
            return $analysis->setMaxSpeed(round($this->maxSpeed, 2));
        }

        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        if ($this->maxSpeed === null || $this->maxSpeed < $point->getSpeed()) {
            $this->maxSpeed = $point->getSpeed();
        }
        return $point;
    }
}
