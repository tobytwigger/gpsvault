<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Elevation extends AnalyserContract
{

    public function canRun(Analysis $analysis): bool
    {
        return true;
    }

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

        $elevationPoints = collect($analysis->getPoints())
            ->filter(fn(Point $point) => $point->getElevation() !== null);

        $previousElevation = null;
        /** @var Point $point */
        foreach ($elevationPoints as $point) {
            if ($maxAltitude === null || $maxAltitude < $point->getElevation()) {
                $maxAltitude = $point->getElevation();
            }
            if ($minAltitude === null || $minAltitude > $point->getElevation()) {
                $minAltitude = $point->getElevation();
            }
            if ($previousElevation !== null) {
                if ($previousElevation < $point->getElevation()) {
                    $elevationGain += $point->getElevation() - $previousElevation;
                } elseif ($previousElevation > $point->getElevation()) {
                    $elevationLoss += $previousElevation - $point->getElevation();
                }
            }
            $previousElevation = $point->getElevation();
        }

        if ($analysis->getMaxAltitude() === null) {
            $analysis->setMaxAltitude($maxAltitude);
        }
        if ($analysis->getMinAltitude() === null) {
            $analysis->setMinAltitude($minAltitude);
        }
        if ($analysis->getCumulativeElevationGain() === null) {
            $analysis->setCumulativeElevationGain($elevationGain);
        }
        if ($analysis->getCumulativeElevationLoss() === null) {
            $analysis->setCumulativeElevationLoss($elevationLoss);
        }
        return $analysis;
    }
}
