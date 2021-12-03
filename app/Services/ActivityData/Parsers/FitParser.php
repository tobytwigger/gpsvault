<?php

namespace App\Services\ActivityData\Parsers;

use adriangibbons\phpFITFileAnalysis;
use App\Models\Activity;
use App\Services\ActivityData\Analysis;
use App\Services\ActivityData\Contracts\Parser;
use App\Services\ActivityData\Point;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use phpGPX\phpGPX;

class FitParser implements Parser
{

    public function __construct(Analysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function analyse(Activity $activity): Analysis
    {
        $fit = new phpFITFileAnalysis($activity->activityFile->getFileContents(), ['input_is_data' => true]);

        $record = $fit->data_mesgs['record'] ?? [];

        foreach ($record['timestamp'] ?? [] as $timestamp)
        {
            $get = fn($key) => data_get($record, sprintf('%s.%u', $key, $timestamp), null);
            $this->analysis->pushPoint(
                (new Point())
                    ->setCadence($get('cadence'))
                    ->setAverageTemperature($get('temperature'))
                    ->setElevation($get('altitude'))
                    ->setHeartRate($get('heart_rate'))
                    ->setLatitude($get('position_lat'))
                    ->setLongitude($get('position_long'))
                    ->setSpeed($get('speed') / 3.6)
                    ->setTime(Carbon::createFromTimestamp($timestamp))
            );
        }

        $this->analysis->setDistance(Arr::last(data_get($record, 'distance', 0)) * 1000)
            ->setAverageSpeed(collect(array_values($record['speed']))->avg() / 3.6)
            ->setAveragePace(0)
            ->setMinAltitude(isset($record['altitude']) ? min($record['altitude']) : null)
            ->setMaxAltitude(isset($record['altitude']) ? min($record['altitude']) : null)
//            ->setCumulativeElevationGain($stats->cumulativeElevationGain)
//            ->setCumulativeElevationLoss($stats->cumulativeElevationLoss)
            ->setStartedAt(Carbon::createFromTimestamp(Arr::first($record['timestamp'])))
            ->setFinishedAt(Carbon::createFromTimestamp(Arr::last($record['timestamp'])))
            ->setDuration((float) Carbon::createFromTimestamp(Arr::first($record['timestamp']))->diffInSeconds(Carbon::createFromTimestamp(Arr::last($record['timestamp']))));

        return $this->analysis;
    }
}
