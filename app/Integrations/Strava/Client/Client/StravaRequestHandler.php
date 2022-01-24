<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Exceptions\StravaRateLimitedException;
use App\Models\User;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Arr;

class StravaRequestHandler
{

    private GuzzleClient $guzzleClient;

    private Authenticator $authenticator;

    private User $user;

    public function __construct(Authenticator $authenticator, User $user)
    {
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => 'https://www.strava.com/api/v3/',
        ]);
        $this->authenticator = $authenticator;
        $this->user = $user;
    }

    public function request(string $method, string $uri, array $options = [], bool $authenticated = true): \Psr\Http\Message\ResponseInterface
    {
        $excluded = [];
        for($i=0;$i<20;$i++) {
            $client = $this->user->availableClient($excluded);
            $excluded[] = $client->id;

            try {
                return $this->handleRequest($client, $method, $uri, $options, $authenticated);
            } catch (StravaRateLimitedException $e) {
                continue;
            }
        }
    }

    private function handleRequest(\App\Integrations\Strava\Models\StravaClient $client, string $method, string $uri, array $options = [], bool $authenticated = true): \Psr\Http\Message\ResponseInterface
    {
        try {
            $response = $this->guzzleClient->request($method, $uri, array_merge([
                'headers' => array_merge(
                    $authenticated ? ['Authorization' => sprintf('Bearer %s', $this->authenticator->getAuthToken($client))] : [],
                    $options['headers'] ?? [])
            ], $options));
            $this->updateRateLimits($response, $client);
            return $response;
        } catch (\Exception $e) {
            if ($e->getCode() === 429) {
                $this->markClientAsLimited($client);
                throw new StravaRateLimitedException();
            }
            throw $e;
        }
    }

    private function markClientAsLimited(\App\Integrations\Strava\Models\StravaClient $client)
    {
        $client->used_15_min_calls = 100;
        $client->save();
    }

    private function updateRateLimits(\Psr\Http\Message\ResponseInterface $response, \App\Integrations\Strava\Models\StravaClient $client)
    {
        // Get the rate limit usage from the header
        if($response->hasHeader('X-RateLimit-Usage')) {
            $usage = explode(',', Arr::first($response->getHeader('X-RateLimit-Usage')));
            if(count($usage) !== 2) {
                throw new \Exception(sprintf('The Strava API must return rate limit usage, %s given.', Arr::first($response->getHeader('X-RateLimit-Limit'))));
            }
            $client->used_15_min_calls = $usage[0];
            $client->used_daily_calls = $usage[1];
            $client->save();
        }
    }

    public function decodeResponse(\Psr\Http\Message\ResponseInterface $response): array
    {
        return json_decode(
            $response->getBody()->getContents(),
            true
        );
    }



}
