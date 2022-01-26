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

        public function exchangeCode(string $code, StravaClientModel $stravaClient): StravaToken
    {
        $response = $this->guzzleClient->request('post', 'https://www.strava.com/oauth/token', [
            'query' => [
                'client_id' => $stravaClient->client_id,
                'client_secret' => $stravaClient->client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ]);

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        return StravaToken::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
            (int)$credentials['athlete']['id']
        );
    }

}
