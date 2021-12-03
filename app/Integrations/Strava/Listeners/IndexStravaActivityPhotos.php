<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class IndexStravaActivityPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->strava = $strava;
    }

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
        $this->strava->setUserId($activity->user_id);
        $photos = $this->strava->client()->getPhotos($activity->getAdditionalData('strava_id'));
        $existingPhotoIds = collect(Arr::wrap($activity->getAdditionalData('strava_photo_ids')));
        foreach($photos as $photo) {
            if(isset($photo['unique_id']) && !$existingPhotoIds->contains($photo['unique_id'])) {
                $activity->appendAdditionalData('strava_photo_ids', $photo['unique_id']);
            }
        }
        $activity->setAdditionalData('strava_is_loading_photos', false);
    }

    public function middleware()
    {
        return [
            new RateLimited('strava')
        ];
    }

}
