<?php

namespace App\Services\Sync;

use App\Models\Sync;
use App\Models\SyncTask;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
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

    abstract public function description(): string;

    abstract public function name(): string;

    abstract public function run();

    public function user(): User
    {
        return $this->user;
    }

    public function setupComponent(): ?string
    {
        return null;
    }

    public static function id(): string
    {
        return md5(static::class);
    }

    public function process(SyncTask $task)
    {
        $this->task = $task;
        $this->user = $task->sync()->user()->firstOrFail();
        $this->line('Processing task');
        $this->run();
    }

    public function line(string $text)
    {
        $this->task->addMessage($text);
    }

    public function toArray()
    {
        return [
            'id' => static::id(),
            'name' => $this->name(),
            'description' => $this->description(),
            'setup_component' => $this->setupComponent()
        ];
    }

    public function processConfig(array $config): array
    {
        return $config;
    }

    protected function config(string $key, $default = null)
    {
        return data_get($this->task->config(), $key, $default);
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

}
