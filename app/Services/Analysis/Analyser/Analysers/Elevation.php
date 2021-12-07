<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Elevation extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $maxAltitude = null;
        $minAltitude = null;
        $elevationGain = 0;
        $elevationLoss = 0;

        $previousElevation = null;
        /** @var Point $point */
        foreach(collect($analysis->getPoints()) as $point) {
            if($maxAltitude === null || $maxAltitude < $point->getElevation()) {
                $maxAltitude = $point->getElevation();
            }
            if($minAltitude === null || $minAltitude > $point->getElevation()) {
                $minAltitude = $point->getElevation();
            }
            if($previousElevation !== null) {
                if($previousElevation < $point->getElevation()) {
                    $elevationGain += $point->getElevation() - $previousElevation;
                } elseif($previousElevation > $point->getElevation()) {
                    $elevationLoss += $previousElevation - $point->getElevation();
                }
            }
            $previousElevation = $point->getElevation();
        }

        $analysis->setMaxAltitude($maxAltitude);
        $analysis->setMinAltitude($minAltitude);
        $analysis->setCumulativeElevationGain($elevationGain);
        $analysis->setCumulativeElevationLoss($elevationLoss);
        return $analysis;
    }
}
