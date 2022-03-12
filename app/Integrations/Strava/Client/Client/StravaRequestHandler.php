<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Exceptions\StravaRateLimitedException;
use App\Models\User;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class StravaRequestHandler
{

    private GuzzleClient $guzzleClient;

    private Authenticator $authenticator;

    private User $user;

    public function __construct(Authenticator $authenticator, User $user, GuzzleClient $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
        $this->authenticator = $authenticator;
        $this->user = $user;
    }

    public function unauthenticatedRequest(string $method, string $uri, array $options): ResponseInterface
    {
        return $this->guzzleClient->request($method, $uri, $options);
    }

    public function request(string $method, string $uri, array $options = []): \Psr\Http\Message\ResponseInterface
    {
        //
        $excluded = [];
        for($i=0;$i<20;$i++) {
            $client = $this->user->availableClient($excluded);
            $excluded[] = $client->id;

            try {
                return $this->handleRequest($client, $method, $uri, $options);
            } catch (StravaRateLimitedException $e) {
                continue;
            }
        }
        throw new ClientNotAvailable();
    }

    private function handleRequest(\App\Integrations\Strava\Client\Models\StravaClient $client, string $method, string $uri, array $options = []): \Psr\Http\Message\ResponseInterface
    {
        try {
            $response = $this->guzzleClient->request($method, $uri, array_merge([
                'headers' => array_merge(
                    ['Authorization' => sprintf('Bearer %s', $this->authenticator->getAuthToken($client))],
                    $options['headers'] ?? [])
            ], $options));
            $this->updateRateLimits($response, $client);
            return $response;
        } catch (ClientException $e) {
            if ($e->getCode() === 429) {
                $this->updateRateLimits($e->getResponse(), $client);
                throw new StravaRateLimitedException();
            }
            throw $e;
        }
    }

    private function updateRateLimits(\Psr\Http\Message\ResponseInterface $response, \App\Integrations\Strava\Client\Models\StravaClient $client)
    {
        if($response->hasHeader('X-RateLimit-Usage')) {
            $usage = explode(',', Arr::first($response->getHeader('X-RateLimit-Usage')));
            if(count($usage) !== 2) {
                throw new \Exception(sprintf('The Strava API must return rate limit usage, %s given.', Arr::first($response->getHeader('X-RateLimit-Usage'))));
            }
            $client->used_15_min_calls = $usage[0];
            $client->used_daily_calls = $usage[1];
        }
        // Get the rate limit usage from the header
        if($response->hasHeader('X-RateLimit-Limit')) {
            $limits = explode(',', Arr::first($response->getHeader('X-RateLimit-Limit')));
            if(count($limits) !== 2) {
                throw new \Exception(sprintf('The Strava API must return rate limit limits, %s given.', Arr::first($response->getHeader('X-RateLimit-Limit'))));
            }
            $client->limit_15_min = $limits[0];
            $client->limit_daily = $limits[1];
        }
        $client->save();
    }

    public function decodeResponse(\Psr\Http\Message\ResponseInterface $response): array
    {
        return json_decode(
            $response->getBody()->getContents(),
            true
        );
    }

    public function getGuzzleClient(): GuzzleClient
    {
        return $this->guzzleClient;
    }



}
