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
        foreach(collect($analysis->getPoints())->filter(fn(Point $point) => $point->getSpeed() !== null) as $point) {
            if($maxSpeed === null || $maxSpeed < $point->getSpeed()) {
                $maxSpeed = $point->getSpeed();
            }
        }

        return $analysis->setMaxSpeed($maxSpeed);
    }

    public function canRun(Analysis $analysis): bool
    {
        return count($analysis->getPoints()) > 0;
    }
}
