<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Models\StravaClient as StravaClientModel;
use App\Models\User;

abstract class Resource
{
    public User $user;

    public StravaRequestHandler $request;

    public function __construct(User $user, StravaRequestHandler $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function withClient(StravaClientModel $client)
    {
        $this->request->withClient($client);
        return $this;
    }
}
