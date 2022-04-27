<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Events\StravaActivityUpdated;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarkActivityAsLoadingPhotos
{
    use Dispatchable, SerializesModels;

    public function handle(StravaActivityUpdated $activityEvent)
    {
        $activityEvent->activity->setAdditionalData('strava_is_loading_photos', true);
    }
}
