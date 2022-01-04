<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class LoadStravaPhotos extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Strava $strava)
    {
        $strava->setUserId($this->activity->user_id);
        $photos = $strava->client($this->stravaClientModel)->getPhotos($this->activity->getAdditionalData('strava_id'));
        $existingPhotoIds = collect(Arr::wrap($this->activity->getAdditionalData('strava_photo_ids')));
        foreach($photos as $photo) {
            if(isset($photo['unique_id']) && !$existingPhotoIds->contains($photo['unique_id'])) {
                $this->activity->appendAdditionalData('strava_photo_ids', $photo['unique_id']);
            }
        }
        $this->activity->setAdditionalData('strava_is_loading_photos', false);
    }

}
