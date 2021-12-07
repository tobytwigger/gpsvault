<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class MaxSpeed extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $maxSpeed = null;
        /** @var Point $point */
        foreach(collect($analysis->getPoints()) as $point) {
            if($maxSpeed === null || $maxSpeed < $point->getSpeed()) {
                $maxSpeed = $point->getSpeed();
            }
        }

        return $analysis->setMaxSpeed($maxSpeed);
    }
}
