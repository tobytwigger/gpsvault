<?php

namespace App\Integrations\Strava\Tasks;

use App\Services\Sync\Task;

class SyncRoutes extends Task
{

    public function description(): string
    {
        return 'Save new routes from Strava.';
    }

    public function name(): string
    {
        return 'Save new Strava routes';
    }
}
