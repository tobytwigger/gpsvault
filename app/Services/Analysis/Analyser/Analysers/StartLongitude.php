<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class StartLongitude extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getStartLongitude() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getStartLongitude() === null) {
            $analysis->setStartLongitude(collect($analysis->getPoints())->first()->getLongitude());
        }

        return $analysis;
    }
}
