<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class Strava
{

    protected string $clientUuid;

    private ConnectionLog $log;

    public function __construct(protected Client $client, protected ClientFactory $clientFactory)
    {
        $this->clientUuid = Uuid::uuid4();
        $this->log = ConnectionLog::create('strava', $this->clientUuid);
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
            'scope' => 'activity:read,read,read_all,profile:read_all,activity:read_all,activity:write',
            'state  ' => $state
        ];

        $this->log->debug('Generating redirect url');

        return sprintf('https://www.strava.com/oauth/authorize?%s', http_build_query($params));
    }

    public function exchangeCode(
        string $clientId,
        string $clientSecret,
        string $code
    ): StravaToken
    {
        $this->log->debug('About to exchange code for token');

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
            $this->log->error(sprintf('Could not get access token from Strava: %s', $e->getMessage()));
            throw $e;
        }

        $this->log->debug('Access token returned by Strava');

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->debug('Decoded access token');

        $this->log->success('Connected account to Strava');

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
        $this->log->debug('About to refresh token');

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
            $this->log->error(sprintf('Could not get refreshed access token from Strava: %s', $e->getMessage()));
            throw $e;
        }

        $this->log->debug('Refreshed access token returned by Strava');

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->debug('Decoded refreshed access token');

        $this->log->success('Access token refreshed successfully');

        $stravaToken = StravaToken::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
        );

        $savedToken = \App\Integrations\Strava\StravaToken::makeFromStravaToken($stravaToken);
        $savedToken->save();

        return $stravaToken;
    }

    public function client(int $userId = null): StravaClient
    {
        if($userId === null) {
            $userId = Auth::id();
        }

        $this->log->debug('Client created ready for connection to Strava');

        return $this->clientFactory->setLog($this->log)->create($userId);
    }

}
