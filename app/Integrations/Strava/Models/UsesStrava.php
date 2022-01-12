<?php

namespace App\Integrations\Strava\Models;


use App\Integrations\Strava\StravaToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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

    public function availableClient(): StravaClient
    {
        if($this->can('manage-strava-clients')) {
            $client = $this->ownedClients()->available()->first()
                ?? $this->sharedClients()->available()->first();
            if($client) {
                return $client;
            }
        }
        if($this->can('use-public-strava-clients')) {
            $client = StravaClient::public()->available()->first();
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
