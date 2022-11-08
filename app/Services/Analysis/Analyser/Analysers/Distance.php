<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Location\Polyline;

class Distance extends AnalyserContract implements PointAnalyser
{
    private Polyline $polyline;

    public function __construct(Polyline $polyline)
    {
        $this->polyline = $polyline;
    }

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        return $analysis->setDistance(round($this->polyline->getLength(new Vincenty()), 2));
    }

    public function processPoint(Point $point): Point
    {
        if ($point->getLatitude() !== null && $point->getLongitude() !== null) {
            $this->polyline->addPoint(new Coordinate($point->getLatitude(), $point->getLongitude()));
        }
        return $point;
    }
}
