<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class AverageHeartrate extends AnalyserContract implements PointAnalyser
{
    protected array $heartRates = [];

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getAverageHeartrate() === null && !empty($this->heartRates);
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $analysis->setAverageHeartrate(
            round(array_sum($this->heartRates)/count($this->heartRates))
        );

        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        $heartRate = $point->getHeartRate();
        if ($heartRate !== null) {
            $this->heartRates[] = $heartRate;
        }
        return $point;
    }
}
