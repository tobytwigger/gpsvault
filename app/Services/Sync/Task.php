<?php

namespace App\Services\Sync;

use App\Models\Sync;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Task
{

    protected Sync $sync;

    private User $user;

    public static function registerTask(string $class)
    {
        app()->tag([$class], 'tasks');
    }

    abstract public function description(): string;

    abstract public function name(): string;

    abstract public function run();

    public function user(): User
    {
        return $this->user;
    }

    public function id(): string
    {
        return static::class;
    }

    public function process(Sync $sync)
    {
        $this->sync = $sync;
        $this->user = $sync->user()->firstOrFail();
        $this->run();
    }

    public function line(string $text)
    {
        $this->sync->updateTaskMessage($this->id(), $text);
    }

}
