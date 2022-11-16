<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class EndLatitude extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getEndLatitude() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $analysis->setEndLatitude(collect($analysis->getPoints())->last()->getLatitude());

        return $analysis;
    }
}
