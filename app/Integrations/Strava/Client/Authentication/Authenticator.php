<?php

namespace App\Integrations\Strava\Client\Authentication;

use App\Integrations\Strava\Client\Models\StravaClient as StravaClientModel;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Validation\UnauthorizedException;

class Authenticator
{

    private User $user;

    private Client $guzzleClient;

    public function __construct(User $user, Client $guzzleClient)
    {
        $this->user = $user;
        $this->guzzleClient = $guzzleClient;
    }

    public function getAuthToken(StravaClientModel $client): string
    {
        $token = $this->user->stravaTokens()->enabled()->where('strava_client_id', $client->id)->orderBy('created_at', 'desc')->first()
            ?? throw new UnauthorizedException('Your account is not connected to Strava.');

        if($token->expired()) {
            $token = $this->refreshToken($token, $client);
        }
        return $token->access_token;
    }

    public function refreshToken(\App\Integrations\Strava\Client\Authentication\StravaToken $token, StravaClientModel $client): StravaToken
    {
        $response = $this->guzzleClient->request('post', 'https://www.strava.com/oauth/token', [
            'query' => [
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
                'refresh_token' => $token->refresh_token,
                'grant_type' => 'refresh_token'
            ]
        ]);

        $credentials = json_decode(
            $response->getBody()->getContents(),
            true
        );

        $stravaToken = StravaTokenResponse::create(
            new Carbon((int) $credentials['expires_at']),
            (int)$credentials['expires_in'],
            (string)$credentials['refresh_token'],
            (string)$credentials['access_token'],
            $this->user->getAdditionalData('strava_athlete_id') ?? throw new \Exception(sprintf('Athlete ID not set for user %u.', $this->user->id))
        );

        $token->updateFromStravaTokenResponse($stravaToken);

        return $token;
    }

}
