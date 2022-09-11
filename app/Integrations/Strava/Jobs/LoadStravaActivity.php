<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\ApiImport;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JobStatus\Trackable;

class LoadStravaActivity extends StravaBaseJob
{
    use Trackable;

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
