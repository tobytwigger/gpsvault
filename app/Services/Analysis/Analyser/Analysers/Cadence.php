<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Cadence extends AnalyserContract implements PointAnalyser
{
    protected array $cadences = [];

    public function canRun(Analysis $analysis): bool
    {
        return !empty($this->cadences) && $analysis->getAverageCadence() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $analysis->setAverageCadence(
            round(array_sum($this->cadences)/count($this->cadences))
        );

        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        $cadence = $point->getCadence();
        if ($cadence !== null && $cadence !== 0.0) {
            $this->cadences[] = $cadence;
        }
        return $point;
    }
}
