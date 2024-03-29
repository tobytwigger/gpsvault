<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\Api\ApiImport;

class LoadStravaActivity extends StravaBaseJob
{
    public function alias(): ?string
    {
        return 'load-strava-activity';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $strava = Strava::client($this->activity->user);

        $activityData = $strava->activity()->getActivity($this->activity->getAdditionalData('strava_id'));

        ApiImport::activity()->import($activityData, $this->activity->user);
    }
}
