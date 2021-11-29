<?php

namespace App\Services\Sync;

use App\Exceptions\TaskCancelled;
use App\Exceptions\TaskSucceeded;
use App\Models\Sync;
use App\Models\SyncTask;
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
        $this->user = $task->sync->user ?? throw new ModelNotFoundException('Could not find the user');
        $this->run();
    }

    public function validationRules(): array
    {
        return [];
    }

    public function requiredConfig(): array
    {
        return [];
    }

    public function fail(string $message)
    {
        throw new \Exception($message);
    }

    public function succeed(string $message)
    {
        $this->task->addMessage($message);
        throw new TaskSucceeded($message);
    }

    public function isChecked(User $user): bool
    {
        return $this->disabled($user) === false;
    }

    public function disableBecause(User $user): ?string
    {
        return null;
    }

    final public function disabled(User $user): bool
    {
        return $this->disableBecause($user) !== null;
    }

    public function offerBail(string $message = null)
    {
        if($this->task->status === 'cancelled') {
            throw new TaskCancelled($message);
        }
    }

    public function runsAfter(): array
    {
        return [];
    }

    public function line(string $text)
    {
        $this->task->addMessage($text);
    }

    public function percentage(int $percentage)
    {
        $this->task->setPercentage($percentage);
    }

    public function toArray()
    {
        return array_merge(
            [
                'id' => static::id(),
                'name' => $this->name(),
                'description' => $this->description(),
                'setup_component' => $this->setupComponent(),
                'required_config' => $this->requiredConfig()
            ],
            Auth::check() ? [
                'checked' => $this->isChecked(Auth::user()),
                'disable_because' => $this->disableBecause(Auth::user()),
                'disabled' => $this->disabled(Auth::user())
            ] : []
        );
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
