<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Client\Models\StravaActivity;
use App\Integrations\Strava\Client\Exceptions\StravaRateLimitedException;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use App\Integrations\Strava\Models\StravaClient as StravaClientModel;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

class StravaClient
{

    protected ?Client $client = null;

    protected User $user;

    protected int $userId;

    protected ConnectionLog $log;

    private StravaClientModel $stravaClientModel;

    public function __construct(int $userId, ConnectionLog $log)
    {
        $this->userId = $userId;
        $this->user = User::findOrFail($this->userId);
        $this->log = $log;
        $this->client = new Client([
            'base_uri' => 'https://www.strava.com/api/v3/',
        ]);
        $this->stravaClientModel = $this->user->availableClient();
    }

    protected function request(string $method, string $uri, array $options = [], bool $authenticated = true): \Psr\Http\Message\ResponseInterface
    {
        $this->log->debug(sprintf('Making a %s request to %s', $method, $uri));

        try {
            return $this->client->request($method, $uri, array_merge([
                'headers' => array_merge(
                    $authenticated ? ['Authorization' => sprintf('Bearer %s', $this->getAuthToken())] : [],
                    $options['headers'] ?? [])
            ], $options));
        } catch (\Exception $e) {
            $this->log->error(sprintf('Request failed with code %d: %s', $e->getCode(), $e->getMessage()));
            if($e->getCode() === 429) {
                throw new StravaRateLimitedException();
            }
            throw $e;
        }
    }

    private function getAuthToken(): string
    {
        $this->log->debug(sprintf('Resolving the auth token from the database'));

        try {
            $token = User::findOrFail($this->userId)->stravaTokens()->orderBy('created_at', 'desc')->firstOrFail();
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

    public function exchangeCode(string $code, StravaClientModel $stravaClient): StravaToken
    {
        $this->log->debug('About to exchange code for token');

        try {
            $response = $this->request('post', 'https://www.strava.com/oauth/token', [
                'query' => [
                    'client_id' => $stravaClient->client_id,
                    'client_secret' => $stravaClient->client_secret,
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
            (int)$credentials['athlete']['id']
        );
    }

    public function refreshToken(\App\Integrations\Strava\StravaToken $token): \App\Integrations\Strava\StravaToken
    {
        $this->log->debug('About to refresh token');

        try {

            $response = $this->request('post', 'https://www.strava.com/oauth/token', [
                'query' => [
                    'client_id' => $this->stravaClientModel->client_id,
                    'client_secret' => $this->stravaClientModel->client_secret,
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
            User::findOrFail($this->userId)->getAdditionalData('strava_athlete_id') ?? throw new \Exception('Athlete ID not set for user ' . $this->userId)
        );

        $token->updateFromStravaToken($stravaToken);

        $this->log->success('Refreshed access token.');

        return $token;
    }

    public function getActivity(int $activityId)
    {
        $this->log->debug(sprintf('About to get activity %d', $activityId));

        $response = $this->request('GET', 'activities/' . $activityId, [
            'query' => [
                'include_all_efforts' => true
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved user activity %d', $activityId));

        return $content;
    }

    public function getActivities(int $page = 1)
    {
        $this->log->debug(sprintf('About to get user activities, page %d', $page));

        $response = $this->request('GET', 'athlete/activities', [
            'query' =>  [
                'page' => $page,
                'per_page' => 10
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved user activities, page %d', $page));

        return $content;
    }

    public function getPhotos(int $activityId)
    {
        $this->log->debug(sprintf('About to get photos for activity %d', $activityId));

        $response = $this->request('GET', 'activities/' . $activityId . '/photos', [
            'query' => [
                'photo_source' => true
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved %u photos for activity %d', count($content), $activityId));

        return $content;
    }

    public function getComments(int $activityId, int $page = 1)
    {
        $this->log->debug(sprintf('About to get page %u of comments for activity %d', $page, $activityId));

        $response = $this->request('GET', 'activities/' . $activityId . '/comments', [
            'query' => [
                'page' => $page,
                'per_page' => 200
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved %u comments for activity %d', count($content), $activityId));

        return $content;
    }

    public function getKudos(int $activityId, int $page = 1)
    {
        $this->log->debug(sprintf('About to get page %u of kudos for activity %d', $page, $activityId));

        $response = $this->request('GET', 'activities/' . $activityId . '/kudos', [
            'query' => [
                'page' => $page,
                'per_page' => 200
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved %u kudos for activity %d', count($content), $activityId));

        return $content;
    }

    public function getActivityStream($activityId)
    {
        $this->log->debug(sprintf('About to get in depth data stream for activity %d', $activityId));

        $response = $this->request('GET', 'activities/' . $activityId . '/streams', [
            'query' => [
                'keys' => 'time,altitude,heartrate,cadence,watts,temp,moving,latlng,distance,velocity_smooth',
                'key_by_type' => false
            ]
        ]);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $this->log->info(sprintf('Retrieved in depth data stream for activity %d', $activityId));

        return $content;
    }

    public function webhookExists(): bool
    {
        $this->log->debug('Checking if a webhook exists');

        $response = $this->request('GET', 'push_subscriptions', [
            'json' => [
                'client_id' => $this->stravaClientModel->client_id,
                'client_secret' => $this->stravaClientModel->client_secret,
            ]
        ], false);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $exists = true;
        if(count($content) === 0) {
            $exists = false;
        }

        $this->log->info(sprintf('The webhook is%s set up.', $exists ? '' : ' not'));

        return $exists;
    }

    public function createWebhook()
    {
        $this->log->debug('Sending webhook creation request');

        $response = $this->request('POST', 'push_subscriptions', [
            'json' => [
                'client_id' => $this->stravaClientModel->client_id,
                'client_secret' => $this->stravaClientModel->client_secret,
                'callback_url' => route('strava.webhook.verify'),
                'verify_token' => $this->stravaClientModel->webhook_verify_token
            ]
        ], false);

        $content = json_decode(
            $response->getBody()->getContents(),
            true
        );

        dd($content);

        $this->log->info(sprintf('Created webhook with an ID of %s', $content['id']));

    }

}
