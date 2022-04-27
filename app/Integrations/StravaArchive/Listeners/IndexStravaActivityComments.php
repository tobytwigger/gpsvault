<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Integrations\Strava\Jobs\LoadStravaComments;

class IndexStravaActivityComments
{

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
        LoadStravaComments::dispatch($activity);
    }
}
