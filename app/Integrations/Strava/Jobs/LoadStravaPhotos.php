<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use Illuminate\Support\Arr;

class LoadStravaPhotos extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(Strava $strava)
    {
        $strava->setUserId($this->activity->user_id);
        $photos = $strava->client($this->stravaClientModel)->getPhotos($this->activity->getAdditionalData('strava_id'));
        $existingPhotoIds = collect(Arr::wrap($this->activity->getAdditionalData('strava_photo_ids')));
        foreach ($photos as $photo) {
            if (isset($photo['unique_id']) && !$existingPhotoIds->contains($photo['unique_id'])) {
                $this->activity->pushToAdditionalDataArray('strava_photo_ids', $photo['unique_id']);
            }
        }
        $this->activity->setAdditionalData('strava_is_loading_photos', false);
    }
}
