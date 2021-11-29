<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Log\ConnectionLog;

class ClientFactory
{

    private ConnectionLog $log;

    public function __construct(ConnectionLog $connectionLog)
    {
        $connectionLog->setIntegration('strava');
        $this->log = $connectionLog;
    }

    public function create(int $userId): StravaClient
    {
        $this->log->setUserId($userId);
        return new StravaClient(
            $userId,
            $this->log
        );
    }
}
