<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class AverageSpeed extends AnalyserContract implements PointAnalyser
{
    protected array $speed = [];

    public function canRun(Analysis $analysis): bool
    {
        return count($this->speed) > 0 && $analysis->getAverageSpeed() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        return $analysis->setAverageSpeed(
            array_sum($this->speed)/count($this->speed)
        );
    }

    public function processPoint(Point $point): Point
    {
        if ($point->getSpeed()) {
            $this->speed[] = $point->getSpeed();
        }

        return $point;
    }
}
