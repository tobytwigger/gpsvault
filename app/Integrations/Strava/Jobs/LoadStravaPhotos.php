<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\Api\ApiImport;

class LoadStravaPhotos extends StravaBaseJob
{
    public function alias(): ?string
    {
        return 'load-strava-photos';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $strava = Strava::client($this->activity->user);
        $photos = $strava->activity()->getPhotos($this->activity->getAdditionalData('strava_id'));
        foreach ($photos as $photo) {
            ApiImport::photos()->import($photo, $this->activity);
        }
    }
}
