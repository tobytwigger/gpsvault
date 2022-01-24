<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Client\Resources\Activity;
use App\Integrations\Strava\Client\Client\Resources\Webhook;
use App\Models\User;

class StravaClient
{

    protected User $user;

    private StravaRequestHandler $requestHandler;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->requestHandler = app(StravaRequestHandler::class, ['user' => $user]);
    }

    private function createHandler(string $class)
    {
        return app($class, [
            'user' => $this->user,
            'request' => $this->requestHandler
        ]);
    }

    public function activity()
    {
        return $this->createHandler(Activity::class);
    }

    public function webhook()
    {
        return $this->createHandler(Webhook::class);
    }



}
