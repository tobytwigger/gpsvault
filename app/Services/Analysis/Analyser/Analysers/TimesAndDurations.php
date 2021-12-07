<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

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

        $analysis->setDuration($endTime->diffInSeconds($startTime));
        $analysis->setStartedAt($startTime);
        $analysis->setFinishedAt($endTime);
        return $analysis;
    }
}
