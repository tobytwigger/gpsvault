<?php

namespace App\Services\Sync;

abstract class Task
{

    public static function registerTask(string $class)
    {
        app()->tag([$class], 'tasks');
    }

    abstract public function description(): string;

    abstract public function name(): string;
}
