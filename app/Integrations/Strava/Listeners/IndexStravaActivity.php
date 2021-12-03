<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class IndexStravaActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->strava = $strava;
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param StravaActivityUpdated $activityEvent
     * @return bool
     */
    public function shouldQueue(StravaActivityUpdated $activityEvent)
    {
        return in_array('strava', $activityEvent->activity->linked_to);
    }

    public function handle(StravaActivityUpdated $activityEvent)
    {
        $activity = $activityEvent->activity->refresh();
        $this->strava->setUserId($activity->user_id);
        $stravaActivity = $this->strava->client()->getActivity($activity->getAdditionalData('strava_id'));

        if(!$activity->description) {
            $activity->description = $stravaActivity['description'];
        } else {
            $activity->description = PHP_EOL . PHP_EOL . 'Imported from Strava: ' . PHP_EOL . $stravaActivity['description'];
        }
        $activity->setAdditionalData('strava_is_loading_details', false);
    }

    public function middleware()
    {
        return [
            new RateLimited('strava')
        ];
    }

}
