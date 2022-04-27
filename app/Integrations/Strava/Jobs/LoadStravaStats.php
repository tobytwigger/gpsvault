<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Services\Analysis\Parser\Point;

class LoadStravaStats extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     *
     */
    public function handle(Strava $strava)
    {
        if ($this->activity->started_at === null) {
            throw new \Exception('The activity must have a start date to retrieve stats from Strava');
        }
        $strava->setUserId($this->activity->user_id);
        $activityStreams = $strava->client($this->stravaClientModel)->getActivityStream($this->activity->getAdditionalData('strava_id'));
        $timeData = $activityStreams['time'] ?? throw new \Exception('No time stream was returned from Strava');

        $points = [];
        foreach ($timeData['data'] ?? [] as $index => $timeDelta) {
            $point = new Point();
            $point->setTime($this->activity->started_at->addSeconds($timeDelta));
            $point->setCadence(data_get($activityStreams, 'cadence.data.' . $index, null));
            $point->setLatitude(data_get($activityStreams, 'latlng.data.' . $index . '.0', null));
            $point->setLongitude(data_get($activityStreams, 'latlng.data.' . $index . '.1', null));
            $point->setSpeed(data_get($activityStreams, 'velocity_smooth.data.' . $index, null));
            $point->setTemperature(data_get($activityStreams, 'temp.data.' . $index, null));
            $point->setCumulativeDistance(data_get($activityStreams, 'distance.data.' . $index, null));
            $point->setElevation(data_get($activityStreams, 'altitude.data.' . $index, null));
            $point->setHeartRate(data_get($activityStreams, 'heartrate.data.' . $index, null));
            $points[] = $point;
        }

        if ($stats = $this->activity->statsFrom('strava')->first()) {
            $stats->waypoints()->delete();

            foreach (collect($points)->chunk(1000) as $chunkedPoints) {
                $stats->waypoints()->createMany($chunkedPoints->map(fn (Point $point) => [
                    'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
                    'elevation' => $point->getElevation(),
                    'time' => $point->getTime(),
                    'cadence' => $point->getCadence(),
                    'temperature' => $point->getTemperature(),
                    'heart_rate' => $point->getHeartRate(),
                    'speed' => $point->getSpeed(),
                    'grade' => $point->getGrade(),
                    'battery' => $point->getBattery(),
                    'calories' => $point->getCalories(),
                    'cumulative_distance' => $point->getCumulativeDistance(),
                ]));
            }
        }
        $this->activity->setAdditionalData('strava_is_loading_stats', false);
    }
}
