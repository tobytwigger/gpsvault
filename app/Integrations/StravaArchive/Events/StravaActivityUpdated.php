<?php

namespace App\Integrations\Strava\Events;

use App\Models\Activity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StravaActivityUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Activity $activity;

    /**
     * Create a new event instance.
     *
     */
    public function __construct(Activity $activity)
    {
        
        $this->activity = $activity;
    }
}
