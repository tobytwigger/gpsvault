<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Location\Polyline;

class Distance extends AnalyserContract
{

    /**
     * @param Analysis $analysis
     * @return Analysis
     */
    protected function run(Analysis $analysis): Analysis
    {
        $polyline = new Polyline();
        collect($analysis->getPoints())->each(fn(Point $point) => $polyline->addPoint(new Coordinate($point->getLatitude(), $point->getLongitude())));
        $distance = $polyline->getLength(new Vincenty());

        return $analysis->setdistance($distance);
    }
}
