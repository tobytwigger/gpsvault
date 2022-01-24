<?php

namespace App\Integrations\Strava\Webhooks;

use App\Integrations\Strava\Client\Import\ImportStravaActivity;
use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Models\User;

class HandleIndexingActivity extends HandleStravaWebhookJob
{

    public function getActivityData(User $user)
    {
        $strava = app(Strava::class);
        $strava->setUserId($user->id);
        return $strava->client($user->availableClient())->getActivity($this->payload->getObjectId());
    }

    public function handle()
    {
        $user = User::whereAdditionalData('strava_athlete_id', $this->payload->getOwnerId())->firstOrFail();
        ImportStravaActivity::importFromApi($this->getActivityData($user), $user, true);
    }
}
