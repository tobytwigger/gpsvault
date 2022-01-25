<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Client\Models\StravaClient;

class RedirectUrl
{


    public function redirectUrl(
        string $state,
        StravaClient $client
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

}
