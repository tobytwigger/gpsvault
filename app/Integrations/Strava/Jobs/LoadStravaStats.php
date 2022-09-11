<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\ApiImport;
use JobStatus\Trackable;

class LoadStravaStats extends StravaBaseJob
{
    public function alias(): ?string
    {
        return 'load-strava-stats';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $strava = Strava::client($this->activity->user);
        $activityStreams = $strava->activity()->getActivityStream($this->activity->getAdditionalData('strava_id'));

        ApiImport::stats()->import($activityStreams, $this->activity);
    }
}
