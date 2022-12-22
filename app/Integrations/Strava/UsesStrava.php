<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait UsesStrava
{
    public static function bootUsesStrava()
    {
        static::deleting(function (User $user) {
            $user->stravaTokens()->delete();
        });
    }

    public function stravaTokens()
    {
        return $this->hasMany(StravaToken::class);
    }

    public function ownedClients()
    {
        return $this->hasMany(StravaClient::class);
    }

    public function sharedClients()
    {
        return $this->belongsToMany(StravaClient::class);
    }

    public function stravaClients(): Collection
    {
        return StravaClient::forUser($this->id)->get();
    }

    public function availableClient(array $excluding = []): StravaClient
    {
        if ($this->can('manage-strava-clients')) {
            $client = $this
                ->ownedClients()
                ->enabled()->withSpaces()
                ->connected($this->id)
                ->excluding($excluding)
                ->first()
                ?? $this->sharedClients()->enabled()->withSpaces()->connected($this->id)->excluding($excluding)->first();
            if ($client) {
                return $client;
            }

            $client = StravaClient::public()->enabled()->withSpaces()->connected($this->id)->excluding($excluding)->first();
            if ($client) {
                return $client;
            }
        }

        $client = \App\Settings\StravaClient::getClientModelOrFail();
        if (!$client->isConnected($this->id) || !$client->hasSpaces()) {
            throw new ClientNotAvailable('No available clients found.');
        }

        return $client;
    }
}
