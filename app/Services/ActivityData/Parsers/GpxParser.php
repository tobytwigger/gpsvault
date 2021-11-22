<?php

namespace App\Services\ActivityData\Parsers;

use App\Models\Activity;
use App\Services\ActivityData\Analysis;
use App\Services\ActivityData\Contracts\Parser;
use App\Services\ActivityData\Point;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use phpGPX\phpGPX;

class GpxParser implements Parser
{

    public function __construct(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function analyse(Activity $activity): Analysis
    {
        $gpx = new phpGPX();
        $file = $gpx->load(Storage::path($activity->filepath));
        foreach ($file->tracks as $track)
        {
            // Statistics for whole track
            $track->stats->toArray();

            foreach ($track->segments as $segment)
            {
                $stats = $segment->stats;

                $this->analysis->setDistance($stats->distance)
                    ->setAverageSpeed($stats->averageSpeed)
                    ->setAveragePace($stats->averagePace)
                    ->setMinAltitude($stats->minAltitude)
                    ->setMaxAltitude($stats->maxAltitude)
                    ->setCumulativeElevationGain($stats->cumulativeElevationGain)
                    ->setCumulativeElevationLoss($stats->cumulativeElevationLoss)
                    ->setStartedAt($stats->startedAt ? Carbon::make($stats->startedAt) : null)
                    ->setFinishedAt($stats->finishedAt ? Carbon::make($stats->finishedAt) : null)
                    ->setDuration($stats->duration);

                foreach($segment->getPoints() as $point) {

                    $this->analysis->pushPoint(
                        (new Point())
                            ->setCadence($point->extensions->trackPointExtension->cad)
                            ->setAverageTemperature($point->extensions->trackPointExtension->aTemp)
                            ->setElevation($point->elevation)
                            ->setHeartRate($point->extensions->trackPointExtension->hr)
                            ->setLatitude($point->latitude)
                            ->setLongitude($point->longitude)
                            ->setSpeed($point->extensions->trackPointExtension->speed)
                            ->setTime($point->time ? Carbon::make($point->time) : null)
                    );
                }
            }
        }

        return $this->analysis;
    }
}
