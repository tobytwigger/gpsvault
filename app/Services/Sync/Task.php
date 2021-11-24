<?php

namespace App\Services\Sync;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Task
{

    public static function registerTask(string $class)
    {
        app()->tag([$class], 'tasks');
    }

    abstract public function description(): string;

    abstract public function name(): string;

    abstract public function run();

    public function user(): User
    {
        return Auth::user();
    }

}
