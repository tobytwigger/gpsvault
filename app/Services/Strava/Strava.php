<?php

namespace App\Services\Strava;

use App\Services\Strava\Authentication\StravaToken;
use App\Services\Strava\Client\StravaClient;
use App\Services\Strava\Log\ConnectionLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class Strava
{

    public function __construct(protected Client $client, protected ClientFactory $clientFactory)
    {
    }

    public function redirectUrl(
        int $clientId,
        string $redirectUrl,
        string $state
    ): string
    {
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'activity:read,read',
            'state  ' => $state
        ];

        app(ConnectionLog::class)->debug('Generating redirect url');

        return sprintf('https://www.strava.com/oauth/authorize?%s', http_build_query($params));
    }

    public function exchangeCode(
        string $clientId,
        string $clientSecret,
        string $code
    ): StravaToken
    {
        app(ConnectionLog::class)->debug('About to exchange code for token');

        try {
            $response = $this->client->request('post', 'https://www.strava.com/oauth/token', [
                'query' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'code' => $code,
                    'grant_type' => 'authorization_code'
                ]
            ]);
        } catch (\Exception $e) {
            app(ConnectionLog::class)->error(sprintf('Could not get access token from Strava: %s', $e->getMessage()));
            throw $e;
        }

        app(ConnectionLog::class)->debug('Access token returned by Strava');

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        app(ConnectionLog::class)->debug('Decoded access token');

        app(ConnectionLog::class)->success('Initial connection to Strava made successfully');

        return StravaToken::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
        );

    }

    public function refreshToken(
        string $clientId,
        string $clientSecret,
        string $refreshToken
    ): StravaToken
    {
        app(ConnectionLog::class)->debug('About to refresh token');

        try {

            $response = $this->client->request('post', 'https://www.strava.com/oauth/token', [
                'query' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type' => 'refresh_token'
                ]
            ]);
        } catch (\Exception $e) {
            app(ConnectionLog::class)->error(sprintf('Could not get refreshed access token from Strava: %s', $e->getMessage()));
            throw $e;
        }

        app(ConnectionLog::class)->debug('Refreshed access token returned by Strava');

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        app(ConnectionLog::class)->debug('Decoded refreshed access token');

        app(ConnectionLog::class)->success('Access token refreshed successfully');

        $stravaToken = StravaToken::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
        );

        $savedToken = \App\Models\StravaToken::makeFromStravaToken($stravaToken);
        $savedToken->save();

        return $stravaToken;
    }

    public function client(int $userId = null): StravaClient
    {
        if($userId === null) {
            $userId = Auth::id();
        }

        app(ConnectionLog::class)->debug('Client created ready for connection to Strava');

        return $this->clientFactory->create($userId);
    }

}
