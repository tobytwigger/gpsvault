<?php

namespace App\Integrations\Strava\Webhooks;

use App\Integrations\Strava\Client\Import\ImportStravaActivity;
use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;

class HandleUpdatedActivity extends HandleStravaWebhookJob
{

    public function getActivityData(Activity $activity)
    {
        $strava = app(Strava::class);
        $strava->setUserId($activity->user_id);
        return $strava->client()->getActivity($activity->getAdditionalData($this->payload->getObjectId()));
    }

    public function handle()
    {
        $activity = Activity::whereAdditionalData('strava_id', $this->payload->getObjectId())->first();
        if($activity) {
            ImportStravaActivity::importFromApi($this->getActivityData($activity), $activity->user);
        }
    }
}
