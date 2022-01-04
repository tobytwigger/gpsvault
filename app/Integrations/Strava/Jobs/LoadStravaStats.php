<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Services\Analysis\Parser\Point;
use App\Services\File\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class LoadStravaStats extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Strava $strava)
    {
        if($this->activity->started_at === null) {
            throw new \Exception('The activity must have a start date to retrieve stats from Strava');
        }
        $strava->setUserId($this->activity->user_id);
        $activityStreams = $strava->client($this->stravaClientModel)->getActivityStream($this->activity->getAdditionalData('strava_id'));
        $timeData = $activityStreams['time'] ?? throw new \Exception('No time stream was returned from Strava');

        $points = [];
        foreach($timeData['data'] ?? [] as $index => $timeDelta) {
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

        if($stats = $this->activity->activityStatsFrom('strava')->first()) {
            $stats->json_points_file_id = Upload::activityPoints($points, $this->activity->user)->id;
            $stats->save();
        }
        $this->activity->setAdditionalData('strava_is_loading_stats', false);
    }

}
