<?php

namespace App\Services\Strava\Log;

use App\Models\ConnectionLog as ConnectionLogModel;
use Illuminate\Support\Facades\Auth;

class ConnectionLog
{

    public function log(string $type, string $message, int $userId = null)
    {
        if($userId === null) {
            $userId = Auth::id();
        }

        ConnectionLogModel::create([
            'type' => $type,
            'log' => $message,
            'user_id' => $userId
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

    public static function __callStatic(string $name, array $arguments)
    {
        if(in_array($name, [
            ConnectionLogModel::WARNING,
            ConnectionLogModel::INFO,
            ConnectionLogModel::ERROR,
            ConnectionLogModel::DEBUG,
            ConnectionLogModel::SUCCESS
        ])) {
            $instance = app(static::class);

            return $instance->{$name}(...$arguments);
        }
        throw new \InvalidArgumentException(sprintf('Method %s not found in ConnectionLogModel', $name));
    }


}
