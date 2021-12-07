<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Locations extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $analysis->setStartLatitude(collect($analysis->getPoints())->first()->getLatitude());
        $analysis->setStartLongitude(collect($analysis->getPoints())->first()->getLongitude());
        $analysis->setEndLatitude(collect($analysis->getPoints())->last()->getLatitude());
        $analysis->setEndLongitude(collect($analysis->getPoints())->last()->getLongitude());
        return $analysis;
    }
}
