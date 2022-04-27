<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class TimesAndDurations extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $startTime = collect($analysis->getPoints())->first()->getTime();
        $endTime = collect($analysis->getPoints())->last()->getTime();

        if ($startTime) {
            if ($analysis->getStartedAt() === null) {
                $analysis->setStartedAt($startTime);
            }
        }
        if ($endTime) {
            if ($analysis->getFinishedAt() === null) {
                $analysis->setFinishedAt($endTime);
            }
        }
        if ($startTime && $endTime) {
            if ($analysis->getDuration() === null) {
                $analysis->setDuration($endTime->diffInSeconds($startTime));
            }
        }

        return $analysis;
    }

    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1;
    }
}
