<?php

namespace App\Integrations\Strava\Webhooks;

use App\Models\Activity;

class HandleDeletedActivity extends HandleStravaWebhookJob
{
    public function handle()
    {
        $activity = Activity::whereAdditionalData('strava_id', $this->payload->getObjectId())->first();
        if ($activity) {
            $linkedTo = $activity->linked_to;
            if (in_array('strava', $linkedTo)) {
                unset($linkedTo[array_search('strava', $linkedTo)]);
            }
            $activity->linked_to = $linkedTo;
            $activity->save();
        }
    }
}
