<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class MovingTime extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return false;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $duration = $analysis->getDuration();
        $distance = $analysis->getDistance();
        $pace = $duration / $distance;

        return $analysis->setAveragePace($pace);
    }
}
