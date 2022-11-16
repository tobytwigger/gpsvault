<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class MaxHeartrate extends AnalyserContract implements PointAnalyser
{
    protected ?float $maxHeartrate = null;

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getMaxHeartrate() === null && $this->maxHeartrate !== null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $analysis->setMaxHeartrate($this->maxHeartrate);

        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        $heartRate = $point->getHeartRate();
        if ($heartRate !== null && ($this->maxHeartrate === null || $this->maxHeartrate < $heartRate)) {
            $this->maxHeartrate = $heartRate;
        }
        return $point;
    }
}
