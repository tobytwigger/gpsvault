<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

class Locations extends AnalyserContract
{
    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 1;
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
        if ($analysis->getStartLongitude() === null) {
            $analysis->setStartLongitude(collect($analysis->getPoints())->first()->getLongitude());
        }
        if ($analysis->getEndLatitude() === null) {
            $analysis->setEndLatitude(collect($analysis->getPoints())->last()->getLatitude());
        }
        if ($analysis->getEndLongitude() === null) {
            $analysis->setEndLongitude(collect($analysis->getPoints())->last()->getLongitude());
        }

        return $analysis;
    }
}
