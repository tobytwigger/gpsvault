<?php

namespace App\Services\Analysis\Parser\Parsers;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use phpGPX\phpGPX;

class GpxParser implements ParserContract
{
    public function read(File $file): Analysis
    {
        $gpx = new phpGPX();
        $file = $gpx->load($file->fullPath());
        $points = collect();

        foreach ($file->tracks as $track) {
            foreach ($track->segments as $segment) {
                foreach ($segment->getPoints() as $point) {
                    $points->push(
                        (new Point())
                            ->setCadence($point->extensions?->trackPointExtension?->cad)
                            ->setTemperature($point->extensions?->trackPointExtension?->aTemp)
                            ->setElevation($point->elevation)
                            ->setHeartRate($point->extensions?->trackPointExtension?->hr)
                            ->setLatitude($point->latitude)
                            ->setLongitude($point->longitude)
                            ->setSpeed($point->extensions?->trackPointExtension?->speed)
                            ->setTime($point->time ? Carbon::make($point->time) : null)
                    );
                }
            }
        }

        $analysis = new Analysis();
        $analysis->setPoints($points->all());

        return $analysis;
    }
}
