<?php

namespace App\Services\Strava;

use App\Models\StravaToken;
use App\Models\Team;
use App\Models\User;
use App\Services\Strava\Client\StravaClient;
use App\Services\Strava\Log\ConnectionLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientFactory
{

    public function __construct()
    {
    }

    public function create(int $teamId): StravaClient
    {
        return new StravaClient(
            $this->getAuthToken($teamId),
            $teamId
        );
    }

    private function getAuthToken(int $teamId): string
    {
        app(ConnectionLog::class)->debug(sprintf('About to get the access token to use'));

        try {
            $token = User::find($teamId)->stravaTokens()->orderBy('created_at', 'desc')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            app(ConnectionLog::class)->error(sprintf('Team not connected to Strava'));
            throw new \Exception('Team not connected to Strava');
        }

        if($token->expired()) {
            app(ConnectionLog::class)->info(sprintf('The access token has expired.'));
            $token = $this->refreshToken($token);
        }
        return $token->access_token;
    }

    private function refreshToken(StravaToken $stravaToken): StravaToken
    {
        app(ConnectionLog::class)->debug(sprintf('About to refresh the access token.'));

        $newToken = $this->strava()->refreshToken(
            config('strava.client_id'),
            config('strava.client_secret'),
            $stravaToken->refresh_token
        );

        app(ConnectionLog::class)->debug(sprintf('Refreshed access token returned by Strava.'));

        $stravaToken->updateFromStravaToken($newToken);
        $stravaToken->save();

        app(ConnectionLog::class)->debug(sprintf('Access token updated'));

        return $stravaToken;
    }

    private function strava(): Strava
    {
        return app(Strava::class);
    }

}
