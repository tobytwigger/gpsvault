<?php

namespace App\Services\ActivityData\Parsers;

use App\Models\Activity;
use App\Services\ActivityData\Analysis;
use App\Services\ActivityData\Contracts\Parser;
use App\Services\ActivityData\Point;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Waddle\Parsers\TCXParser as BaseParser;

class TcxParser implements Parser
{

    public function __construct(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function analyse(Activity $activity): Analysis
    {
        $parser = new BaseParser();
        $file = $parser->parse(Storage::path($activity->filepath));

        $this->analysis->setDistance($file->getTotalDistance())
            ->setAverageSpeed($file->getAverageSpeedInKPH() / 3.6)
            ->setAveragePace($file->getTotalDuration() / ($file->getTotalDistance() / 1000))
//            ->setMinAltitude($file->e)
//            ->setMaxAltitude(max($record['altitude'] ?? []))
//            ->setCumulativeElevationGain($stats->cumulativeElevationGain)
//            ->setCumulativeElevationLoss($stats->cumulativeElevationLoss)
            ->setStartedAt(Carbon::createFromTimestamp($file->getStartTime('U')))
//            ->setFinishedAt(Carbon::createFromTimestamp(Arr::last($record['timestamp'])))
            ->setDuration($file->getTotalDuration());

        foreach($file->getLaps() as $lap) {
            foreach($lap->getTrackPoints() as $trackPoint) {
                $this->analysis->pushPoint(
                    (new Point())
                        ->setCadence($trackPoint->getCadence())
                        ->setElevation($trackPoint->getAltitude())
                        ->setHeartRate($trackPoint->getHeartRate())
                        ->setLatitude($trackPoint->getPosition('lat'))
                        ->setLongitude($trackPoint->getPosition('lon'))
                        ->setSpeed($trackPoint->getSpeed())
                        ->setTime(Carbon::createFromTimestamp($trackPoint->getTime('U')))
                );
            }
        }

        return $this->analysis;
    }
}
