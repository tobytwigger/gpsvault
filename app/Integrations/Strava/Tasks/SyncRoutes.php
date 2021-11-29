<?php

namespace App\Integrations\Strava\Tasks;

use App\Models\User;
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

    public function disableBecause(User $user): ?string
    {
        if(!$user->stravaTokens()->exists()) {
            return 'Your account must be connected to Strava';
        }
        return null;
    }

    public function run()
    {
        throw new \Exception('You must have a premium Strava account to use Strava routes.');
    }
}
