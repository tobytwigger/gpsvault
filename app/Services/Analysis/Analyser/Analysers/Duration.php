<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class Duration extends AnalyserContract
{
    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $startTime = collect($analysis->getPoints())->first()->getTime();
        $endTime = collect($analysis->getPoints())->last()->getTime();

        if ($startTime && $endTime) {
            $analysis->setDuration($endTime->diffInSeconds($startTime));
        }

        return $analysis;
    }

    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1 && $analysis->getDuration() === null;
    }
}
