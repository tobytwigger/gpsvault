<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class StartLatitude extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getStartLatitude() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getStartLatitude() === null) {
            $analysis->setStartLatitude(collect($analysis->getPoints())->first()->getLatitude());
        }

        return $analysis;
    }
}
