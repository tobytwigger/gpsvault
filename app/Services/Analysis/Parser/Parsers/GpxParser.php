<?php

namespace App\Services\Analysis\Parser\Parsers;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use phpGPX\phpGPX;
use Ramsey\Uuid\Uuid;

class GpxParser implements ParserContract
{
    public function read(File $file): Analysis
    {
        $gpx = new phpGPX();
        if ($file->disk === 's3') {
            $localPath = Uuid::getFactory()->uuid4();
            Storage::disk('local')->put($localPath, $file->getFileContents());
            $file = $gpx->load(Storage::disk('local')->path($localPath));
            Storage::delete($localPath);
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
