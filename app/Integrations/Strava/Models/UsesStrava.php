<?php

namespace App\Integrations\Strava\Models;


use App\Integrations\Strava\StravaToken;
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

    public function availableClient(): StravaClient
    {

        return StravaClient::forUser($this->id)->firstOrFail();
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
