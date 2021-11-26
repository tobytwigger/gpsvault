<?php

namespace App\Integrations\Strava;

use App\Models\Activity;
use App\Models\User;
use App\Integrations\Integration;

class StravaIntegration extends Integration
{

    public function id(): string
    {
        return 'strava';
    }

    public function serviceUrl(): string
    {
        return 'https://www.strava.com/';
    }

    public function name(): string
    {
        return 'Strava';
    }

    public function description(): string
    {
        return 'Strava is xyz';
    }

    public function functionality(): array
    {
        return [
            'Import new activities from Strava',
            'Export new activities to Strava'
        ];
    }

    public function connected(User $user): bool
    {
        return $user->stravaTokens()->exists();
    }

    public function loginUrl(): string
    {
        return route('strava.login');
    }

    public function disconnect(User $user): void
    {
        $user->stravaTokens()->withoutGlobalScope('enabled')->withoutGlobalScope('not-expired')->delete();
    }

}
