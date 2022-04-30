<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Client\StravaRequestHandler;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Auth;

class StravaClientFactory
{
    private Repository $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function client(?User $user = null): StravaClient
    {
        $user = $user ?? (
            Auth::check()
                ? Auth::user()
                : throw new Exception('No user has been given to the Strava client')
        );

        return new StravaClient(
            $user,
            app(StravaRequestHandler::class, [
                'user' => $user,
                'guzzleClient' => new Client(['base_uri' => $this->config->get('services.strava.base_url')]),
            ])
        );
    }
}
