<?php

namespace App\Integrations\Strava\Import\Resources;

use App\Services\Analysis\Parser\Point;
use MStaack\LaravelPostgis\Geometries\LineString;

class Stats
{
    public function import(array $statsData, \App\Models\Activity $activity): Stats
    {
        if ($activity->started_at === null) {
            throw new \Exception('The activity must have a start date to retrieve stats from Strava');
        }

        $timeData = $statsData['time'] ?? throw new \Exception('No time stream was returned from Strava');

        $stats = $activity->statsFrom('strava')->firstOrFail();
        $stats->activityPoints()->delete();
        $order = 0;
        $points = [];
        foreach ($this->getPoints($statsData, $timeData, $activity) as $chunkedPoints) {
            \Log::info('Processing');
            $stats->activityPoints()->createMany(collect($chunkedPoints)->map(function (Point $point) use (&$order, &$points) {
                $toReturn = [
                    'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
                    'elevation' => $point->getElevation(),
                    'time' => $point->getTime(),
                    'cadence' => $point->getCadence(),
                    'temperature' => $point->getTemperature(),
                    'heart_rate' => $point->getHeartRate(),
                    'speed' => $point->getSpeed(),
                    'grade' => $point->getGrade(),
                    'order' => $order,
                    'battery' => $point->getBattery(),
                    'calories' => $point->getCalories(),
                    'cumulative_distance' => $point->getCumulativeDistance(),
                ];
                $points[] = new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude(), $point->getElevation());
                $order += 1;

                return $toReturn;
            }));
        }
        \Log::info(count($points));
        if (count($points) > 1) {
            $stats->linestring = new LineString($points);
            $stats->save();
        }

        return $this;
    }

    private function getPoints(array $statsData, array $timeData, \App\Models\Activity $activity): \Generator
    {
        foreach (collect($timeData['data'])->chunk(1000) ?? [] as $chunkedTimeData) {
            yield $chunkedTimeData->mapWithKeys(fn ($timeDelta, $index) => [$index => (new Point())
                ->setTime($activity->started_at->addSeconds($timeDelta))
                ->setCadence(data_get($statsData, 'cadence.data.' . $index, null))
                ->setLatitude(data_get($statsData, 'latlng.data.' . $index . '.0', null))
                ->setLongitude(data_get($statsData, 'latlng.data.' . $index . '.1', null))
                ->setSpeed(data_get($statsData, 'velocity_smooth.data.' . $index, null))
                ->setTemperature(data_get($statsData, 'temp.data.' . $index, null))
                ->setCumulativeDistance(data_get($statsData, 'distance.data.' . $index, null))
                ->setElevation(data_get($statsData, 'altitude.data.' . $index, null))
                ->setHeartRate(data_get($statsData, 'heartrate.data.' . $index, null)),
            ])->all();
        }
    }
}
