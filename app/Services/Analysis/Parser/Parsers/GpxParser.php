<?php

namespace App\Services\Analysis\Parser\Parsers;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use phpGPX\phpGPX;

class GpxParser implements ParserContract
{
    public function read(File $file): Analysis
    {
        $gpx = new phpGPX();
        if($file->disk === 's3') {
            $localPath = tempnam(sys_get_temp_dir(), 'gpx_parser');
            Storage::put($localPath, $file->getFileContents());
            $file = $gpx->load($localPath);
        } else {
            $file = $gpx->load($file->fullPath());
        }
        $points = collect();

        foreach ($file->tracks as $track) {
            foreach ($track->segments as $segment) {
                foreach ($segment->getPoints() as $point) {
                    $points->push(
                        $this->createPoint($point)
                    );
                }
            }
        }

        $analysis = new Analysis();
        $analysis->setPoints($points->all());

        return $analysis;
    }

    private function createPoint(\phpGPX\Models\Point $point): Point
    {
        return (new Point())
            ->setCadence($point->extensions?->trackPointExtension?->cad)
            ->setTemperature($point->extensions?->trackPointExtension?->aTemp)
            ->setElevation($point->elevation)
            ->setHeartRate($point->extensions?->trackPointExtension?->hr)
            ->setLatitude($point->latitude)
            ->setLongitude($point->longitude)
            ->setSpeed($point->extensions?->trackPointExtension?->speed)
            ->setTime($point->time ? Carbon::make($point->time) : null);
    }
}
