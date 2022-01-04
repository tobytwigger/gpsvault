<?php

namespace App\Integrations\Strava\Client\Log;

use App\Models\ConnectionLog as ConnectionLogModel;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class ConnectionLog
{

    private ?string $integration;
    private ?int $userId;

    private array $afterModelSaved = [];

    public function __construct(?string $integration = null)
    {
        $this->integration = $integration;
    }

    public function afterModelSaved(\Closure $callback): static
    {
        $this->afterModelSaved[] = $callback;
        return $this;
    }

    public function log(string $type, string $message, int $userId = null)
    {
        if(!$this->integration) {
            throw new \Exception('The integration name has not been set for the connection log');
        }
        $model = ConnectionLogModel::create([
            'type' => $type,
            'log' => $message,
            'user_id' => $userId ?? $this->getUserId(),
            'integration' => $this->integration
        ]);
        if(count($this->afterModelSaved) > 0) {
            foreach($this->afterModelSaved as $callback) {
                call_user_func($callback, $model);
            }
        }
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

    public static function create(string $integration): ConnectionLog
    {
        return new static($integration);
    }

    public function setIntegration(string $integration): ConnectionLog
    {
        $this->integration = $integration;
        return $this;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getUserId(): int
    {
        $userId = $this->userId ?? Auth::id();
        if($userId) {
            return $userId;
        }
        throw new \Exception('Could not find a user to use for the connection log');
    }

    /**
     * @param int|null $userId
     */
    public function setUserId(?int $userId): ConnectionLog
    {
        $this->userId = $userId;
        return $this;
    }

}
