<?php

namespace App\Integrations\Strava\Client\Log;

use App\Models\ConnectionLog as ConnectionLogModel;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ConnectionLog
{

    private ?string $clientUuid;
    private ?string $requestUuid;
    private string $integration;

    public function __construct(string $integration, string $clientUuid = null, string $requestUuid = null)
    {
        $this->clientUuid = $clientUuid;
        $this->requestUuid = $requestUuid;
        $this->integration = $integration;
    }

    public function startRequest()
    {
        $this->requestUuid = Uuid::uuid4();
    }

    public function endRequest()
    {
        Uuid::uuid4();
    }

    public function log(string $type, string $message, int $userId = null)
    {
        if($userId === null) {
            $userId = Auth::id();
        }

        ConnectionLogModel::create([
            'type' => $type,
            'log' => $message,
            'user_id' => $userId,
            'client_uuid' => $this->clientUuid,
            'request_uuid' => $this->requestUuid,
            'integration' => $this->integration
        ]);
    }

    public function success(string $log, int $userId = null)
    {
        $this->log(ConnectionLogModel::SUCCESS, $log, $userId);
    }

    public function debug(string $log, int $userId = null)
    {
        $this->log(ConnectionLogModel::DEBUG, $log, $userId);
    }

    public function info(string $log, int $userId = null)
    {
        $this->log(ConnectionLogModel::INFO, $log, $userId);
    }

    public function warning(string $log, int $userId = null)
    {
        $this->log(ConnectionLogModel::WARNING, $log, $userId);
    }

    public function error(string $log, int $userId = null)
    {
        $this->log(ConnectionLogModel::ERROR, $log, $userId);
    }

    public static function create(string $integration, string $clientUuid = null, string $requestUuid = null): ConnectionLog
    {
        return new static($integration, $clientUuid, $requestUuid);
    }


}
