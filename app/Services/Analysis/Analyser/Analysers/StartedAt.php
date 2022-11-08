<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class StartedAt extends AnalyserContract
{
    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $startTime = collect($analysis->getPoints())->first()->getTime();

        if ($startTime) {
            $analysis->setStartedAt($startTime);
        }

        return $analysis;
    }

    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getStartedAt() === null;
    }
}
