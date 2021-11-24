<?php

namespace App\Integrations\Strava\Tasks;

use App\Services\Sync\Task;

class SaveNewActivities extends Task
{

    public function description(): string
    {
        return 'Save any new Strava activities and associated information';
    }

    public function name(): string
    {
        return 'Save new Strava activities';
    }
}
