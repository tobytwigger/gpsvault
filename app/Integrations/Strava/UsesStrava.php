<?php

namespace App\Integrations\Strava;


use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

trait UsesStrava
{

    public static function bootUsesStrava()
    {
        static::deleting(function(User $user) {
            $user->stravaTokens()->delete();
        });
    }

    public function stravaClients(): Collection
    {
        return StravaClient::forUser($this->id)->get();
    }

    public function stravaTokens()
    {
        return $this->hasMany(StravaToken::class);
    }

    public function availableClient(array $excluding = []): StravaClient
    {
        if($this->can('manage-strava-clients')) {
            $client = $this
                ->ownedClients()
                ->available()
                ->excluding($excluding)
                ->first()
                ?? $this->sharedClients()->available()->excluding($excluding)->first();
            if($client) {
                return $client;
            }
        }
        if($this->can('use-public-strava-clients')) {
            $client = StravaClient::public()->available()->excluding($excluding)->first();
            if($client) {
                return $client;
            }
        }

        return \App\Settings\StravaClient::getClientModelOrFail();
    }

    public function ownedClients()
    {
        return $this->hasMany(StravaClient::class);
    }

    public function sharedClients()
    {
        return $this->belongsToMany(StravaClient::class);
    }

}
