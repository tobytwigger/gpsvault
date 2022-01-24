<?php

namespace App\Integrations\Strava\Events;

use App\Models\Activity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StravaActivityPhotosUpdated extends StravaActivityUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

}
