<?php

namespace App\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Client\Resources\Activity;
use App\Integrations\Strava\Client\Client\Resources\Webhook;
use App\Models\User;

class StravaClient
{

    protected User $user;

    private StravaRequestHandler $requestHandler;

    public function __construct(User $user, StravaRequestHandler $requestHandler)
    {
        $this->user = $user;
        $this->requestHandler = $requestHandler;
    }

    private function createHandler(string $class)
    {
        return app($class, [
            'user' => $this->user,
            'request' => $this->requestHandler
        ]);
    }

    public function activity(): Activity
    {
        return $this->createHandler(Activity::class);
    }

    public function webhook(): Webhook
    {
        return $this->createHandler(Webhook::class);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRequestHandler(): StravaRequestHandler
    {
        return $this->requestHandler;
    }


}
