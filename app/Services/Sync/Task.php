<?php

namespace App\Services\Sync;

use App\Exceptions\TaskCancelled;
use App\Exceptions\TaskSucceeded;
use App\Services\Sync\Sync;
use App\Services\Sync\SyncTask;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

abstract class Task implements Jsonable, Arrayable
{

    protected SyncTask $task;

    private User $user;

    public static function registerTask(string $class)
    {
        if(!is_a($class, Task::class, true)) {
            throw new \Exception(sprintf('Task class [%s] must extend the base task class', $class));
        }
        $alias = 'tasks.' . $class::id();
        app()->bind($alias, $class);
        app()->tag([$alias], 'tasks');
    }

    abstract public function run();

    public function user(): User
    {
        return $this->user;
    }

    public static function id(): string
    {
        return md5(static::class);
    }

    public function process(SyncTask $task)
    {
        $this->task = $task;
        $this->user = $task->sync->user ?? throw new ModelNotFoundException('Could not find the user');
        $this->run();
    }

    public function validationRules(): array
    {
        return [];
    }

    public function fail(string $message)
    {
        throw new \Exception($message);
    }

    public function succeed(string $message)
    {
        throw new TaskSucceeded($message);
    }

    public function offerBail(string $message = null)
    {
        if($this->task->status === 'cancelled') {
            throw new TaskCancelled($message);
        }
    }

    public function line(string $text)
    {
        $this->task->addMessage($text);
    }

    public function percentage(int $percentage)
    {
        $this->task->setPercentage($percentage);
    }

    protected function config(string $key, $default = null)
    {
        return data_get($this->task->config(), $key, $default);
    }

    public function toArray(): array
    {
        return [
            'id' => static::id()
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

}
