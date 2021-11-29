<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Client\Models\StravaActivity;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

class StravaClient
{

    protected ?Client $client = null;

    public function __construct(private int $userId, private ConnectionLog $log)
    {
    }

    protected function client(): Client
    {
        if($this->client === null) {
            $this->client = new Client([
                'base_uri' => 'https://www.strava.com/api/v3/',
            ]);
        }
        return $this->client;
    }

    protected function request(string $method, string $uri, array $options = [], bool $authenticated = true): \Psr\Http\Message\ResponseInterface
    {
        $this->log->debug(sprintf('Making a %s request to %s', $method, $uri));

        try {
            return $this->client()->request($method, $uri, array_merge([
                'headers' => array_merge(
                    $authenticated ? ['Authorization' => sprintf('Bearer %s', $this->getAuthToken())] : [],
                    $options['headers'] ?? [])
            ], $options));
        } catch (\Exception $e) {
            $this->log->error(sprintf('Request failed with code %d: %s', $e->getCode(), $e->getMessage()));
            throw $e;
        }
    }

    private function getAuthToken(): string
    {
        $this->log->debug(sprintf('Resolving the auth token from the database'));

        try {
            $token = User::find($this->userId)->stravaTokens()->orderBy('created_at', 'desc')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $this->log->error(sprintf('User not connected to Strava'));
            throw new UnauthorizedException('Your account is not connected to Strava');
        }

        if($token->expired()) {
            $this->log->info(sprintf('The access token has expired.'));
            $token = $this->refreshToken($token);
        }
        return $token->access_token;
    }

    public function exchangeCode(string $code): StravaToken
    {
        $this->log->debug('About to exchange code for token');

        try {
            $response = $this->request('post', 'https://www.strava.com/oauth/token', [
                'query' => [
                    'client_id' => config('strava.client_id'),
                    'client_secret' => config('strava.client_secret'),
                    'code' => $code,
                    'grant_type' => 'authorization_code'
                ]
            ], false);
        } catch (\Exception $e) {
            $this->log->error(sprintf('Could not get access token from Strava: %s', $e->getMessage()));
            throw $e;
        }

        $this->log->debug('Access token returned from Strava');

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->success('Connected account to Strava');

        return StravaToken::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
        );

    }

    public function refreshToken(\App\Integrations\Strava\StravaToken $token): \App\Integrations\Strava\StravaToken
    {
        $this->log->debug('About to refresh token');

        try {

            $response = $this->request('post', 'https://www.strava.com/oauth/token', [
                'query' => [
                    'client_id' => config('strava.client_id'),
                    'client_secret' => config('strava.client_secret'),
                    'refresh_token' => $token->refresh_token,
                    'grant_type' => 'refresh_token'
                ]
            ], false);
        } catch (\Exception $e) {
            $this->log->error(sprintf('Could not get refreshed access token from Strava: %s', $e->getMessage()));
            throw $e;
        }

        $this->log->debug('Refreshed access token returned by Strava');

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );


        $stravaToken = StravaToken::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
        );

        $token->updateFromStravaToken($stravaToken);

        $this->log->success('Refreshed access token.');

        return $token;
    }


    public function getActivities(int $page = 1)
    {
        $this->log->debug(sprintf('About to get user activities, page %d', $page));

        $response = $this->request('GET', 'athlete/activities', [
            'query' =>  [
                'page' => $page,
                'per_page' => 50
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved user activities, page %d', $page));

        return $content;
    }

}
