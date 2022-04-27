<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Models\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static StravaClient client(?User $user = null) Get a strava client for the given or authenticated user.
 */
class Strava extends Facade
{
    protected static function getFacadeAccessor()
    {
        return StravaClientFactory::class;
    }
}
