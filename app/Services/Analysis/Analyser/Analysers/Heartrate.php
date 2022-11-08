<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Heartrate extends AnalyserContract implements PointAnalyser
{
    protected array $heartRates = [];

    protected ?float $maxHeartrate = null;

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if (!empty($this->heartRates)) {
            $analysis->setAverageHeartrate(
                round(array_sum($this->heartRates)/count($this->heartRates))
            );
        }
        if ($this->maxHeartrate !== null) {
            $analysis->setMaxHeartrate($this->maxHeartrate);
        }

        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        $heartRate = $point->getHeartRate();
        if ($heartRate !== null) {
            $this->heartRates[] = $heartRate;
            if ($this->maxHeartrate === null || $this->maxHeartrate < $heartRate) {
                $this->maxHeartrate = $heartRate;
            }
        }
        return $point;
    }
}
