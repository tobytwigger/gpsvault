<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class Pace extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $duration = $analysis->getMovingTime();
        $distance = $analysis->getDistance();
        $pace = $duration / $distance;

        return $analysis->setAveragePace(round($pace, 2));
    }

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getMovingTime() !== null
            && $analysis->getDistance() !== null
            && $analysis->getDistance() !== 0.0
            && $analysis->getAveragePace() === null;
    }
}
