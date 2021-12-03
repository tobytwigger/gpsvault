<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkActivityAsLoadingDetails
{
    use Dispatchable, SerializesModels;

    public function handle(StravaActivityUpdated $activityEvent)
    {
        $activityEvent->activity->setAdditionalData('strava_is_loading_details', true);
    }
}
