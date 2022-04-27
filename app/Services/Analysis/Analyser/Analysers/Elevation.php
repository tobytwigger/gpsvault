<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

class Elevation extends AnalyserContract implements PointAnalyser
{
    private ?float $maxAltitude = null;

    private ?float $minAltitude = null;

    private ?float $previousElevation = null;

    private ?float $elevationGain = 0;

    private ?float $elevationLoss = 0;

    public function processPoint(Point $point): void
    {
        if ($point->getElevation() !== null) {
            if ($this->maxAltitude === null || $this->maxAltitude < $point->getElevation()) {
                $this->maxAltitude = $point->getElevation();
            }
            if ($this->minAltitude === null || $this->minAltitude > $point->getElevation()) {
                $this->minAltitude = $point->getElevation();
            }
            if ($this->previousElevation !== null) {
                if ($this->previousElevation < $point->getElevation()) {
                    $this->elevationGain += $point->getElevation() - $this->previousElevation;
                } elseif ($this->previousElevation > $point->getElevation()) {
                    $this->elevationLoss += $this->previousElevation - $point->getElevation();
                }
            }
            $this->previousElevation = $point->getElevation();
        }
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        if ($analysis->getMaxAltitude() === null) {
            $analysis->setMaxAltitude($this->maxAltitude);
        }
        if ($analysis->getMinAltitude() === null) {
            $analysis->setMinAltitude($this->minAltitude);
        }
        if ($analysis->getCumulativeElevationGain() === null) {
            $analysis->setCumulativeElevationGain($this->elevationGain);
        }
        if ($analysis->getCumulativeElevationLoss() === null) {
            $analysis->setCumulativeElevationLoss($this->elevationLoss);
        }

        return $analysis;
    }
}
