<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class EndLongitude extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getEndLongitude() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getEndLongitude() === null) {
            $analysis->setEndLongitude(collect($analysis->getPoints())->last()->getLongitude());
        }

        return $analysis;
    }
}
