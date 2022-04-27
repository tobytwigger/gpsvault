<?php

namespace App\Integrations\Strava\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StravaActivityPhotosUpdated extends StravaActivityUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
