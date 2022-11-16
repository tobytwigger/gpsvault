<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class FinishedAt extends AnalyserContract
{
    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $endTime = collect($analysis->getPoints())->last()->getTime();

        if ($endTime) {
            $analysis->setFinishedAt($endTime);
        }

        return $analysis;
    }

    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getFinishedAt() === null;
    }
}
