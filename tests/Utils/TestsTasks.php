<?php

namespace Tests\Utils;

use App\Models\User;

trait TestsTasks
{

    public function task(string $task, ?User $user = null): TestTaskProxy
    {
        $proxy =  new TestTaskProxy($task);
        if($user) {
            $proxy->forUser($user);
        }
        return $proxy;
    }


}
