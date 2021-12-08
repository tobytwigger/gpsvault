<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Pace extends AnalyserContract
{

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getDuration() !== null
            && $analysis->getDistance() !== null
            && $analysis->getAverageSpeed() === null;
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
