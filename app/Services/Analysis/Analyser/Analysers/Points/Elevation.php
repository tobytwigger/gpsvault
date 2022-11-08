<?php

namespace App\Services\Analysis\Analyser\Analysers\Points;

use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\PointAnalyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Services\Valhalla\Valhalla;
use Location\Coordinate;
use Location\Distance\Vincenty;

class Elevation extends AnalyserContract implements PointAnalyser
{

    private bool $processElevation = false;

    protected function run(Analysis $analysis): Analysis
    {
        if($this->processElevation) {
            $chunkedPointGroups = collect($analysis->getPoints())->filter(fn(Point $point) => $point->getElevation() === null)->chunk(1000);
            $points = [];
            foreach($chunkedPointGroups as $chunkedPoints) {
                $linestring = GooglePolylineEncoder::encode(
                    $chunkedPoints->map(fn(Point $point) => [
                        'lat' => $point->getLatitude(),
                        'lon' => $point->getLongitude()
                    ])->all(),
                6);

                $elevation = (new Valhalla())
                    ->elevationForLineString($linestring)['range_height'];

                foreach($chunkedPoints as $index => $point) {
                    $point->setElevation($elevation[$index][1]);
                    $points[] = $point;
                }
            }
            $analysis->setPoints($points);
        }
        return $analysis;
    }

    public function processPoint(Point $point): Point
    {
        if($point->getElevation() === null) {
            $this->processElevation = true;
        }
        return $point;
    }
}
