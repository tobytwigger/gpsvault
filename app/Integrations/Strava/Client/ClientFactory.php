<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\StravaToken;
use App\Models\Team;
use App\Models\User;
use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

class ClientFactory
{

    private ConnectionLog $log;

    public function setLog(ConnectionLog $log): ClientFactory
    {
        $this->log = $log;
        return $this;
    }

    private function log(): ConnectionLog
    {
        return $this->log ?? app(ConnectionLog::class, ['integration' => 'strava']);
    }

    public function create(int $teamId): StravaClient
    {
        return new StravaClient(
            $this->getAuthToken($teamId),
            $teamId,
            clone $this->log()
        );
    }

    private function getAuthToken(int $teamId): string
    {
        $this->log()->debug(sprintf('About to get the access token to use'));

        try {
            $token = User::find($teamId)->stravaTokens()->orderBy('created_at', 'desc')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $this->log()->error(sprintf('User not connected to Strava'));
            throw new UnauthorizedException('Your account is not connected to Strava');
        }

        if($token->expired()) {
            $this->log()->info(sprintf('The access token has expired.'));
            $token = $this->refreshToken($token);
        }
        return $token->access_token;
    }

    private function refreshToken(StravaToken $stravaToken): StravaToken
    {
        $this->log()->debug(sprintf('About to refresh the access token.'));

        $newToken = $this->strava()->refreshToken(
            config('strava.client_id'),
            config('strava.client_secret'),
            $stravaToken->refresh_token
        );

        $this->log()->debug(sprintf('Refreshed access token returned by Strava.'));

        $stravaToken->updateFromStravaToken($newToken);
        $stravaToken->save();

        $this->log()->debug(sprintf('Access token updated'));
        $this->log()->info(sprintf('Refreshed access token.'));

        return $stravaToken;
    }

    private function strava(): Strava
    {
        return app(Strava::class);
    }

}
