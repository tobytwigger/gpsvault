<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StravaClientFactory
{

    public function client(?User $user = null): StravaClient
    {
        $user = $user ?? (
            Auth::check()
                ? Auth::user()
                : throw new \Exception('No user has been given to the Strava client')
            );
        return new StravaClient($user);
    }

}
