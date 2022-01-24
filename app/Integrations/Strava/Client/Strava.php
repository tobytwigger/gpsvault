<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Models\StravaClient as StravaClientModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Strava
{

    public function redirectUrl(
        string $state,
        StravaClientModel $client
    ): string
    {
        $params = [
            'client_id' => $client->client_id,
            'redirect_uri' => $client->redirectUrl(),
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'activity:read,read,read_all,profile:read_all,activity:read_all,activity:write',
            'state' => $state
        ];

        return sprintf('https://www.strava.com/oauth/authorize?%s', http_build_query($params));
    }

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
