<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Temperature extends AnalyserContract implements PointAnalyser
{
    protected array $temperatures = [];

    public function canRun(Analysis $analysis): bool
    {
        return $analysis->getAverageTemp() === null;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if (!empty($this->temperatures)) {
            $analysis->setAverageTemp(
                round(array_sum($this->temperatures)/count($this->temperatures))
            );
        }

        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        $temperature = $point->getTemperature();
        if ($temperature !== null) {
            $this->temperatures[] = $temperature;
        }
        return $point;
    }
}
