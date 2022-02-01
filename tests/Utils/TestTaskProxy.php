<?php

namespace Tests\Utils;

use App\Models\User;
use App\Services\Sync\RunSyncTask;
use App\Services\Sync\Sync;
use App\Services\Sync\SyncTask;
use App\Services\Sync\Task;
use PHPUnit\Framework\Assert;

class TestTaskProxy
{

    private string $taskName;

    private SyncTask $task;

    private User $user;

    public function __construct(string $task)
    {
        if(!is_a($task, Task::class, true)) {
            throw new \Exception('Task ' . $task . ' does not extend task class.');
        }
        $this->taskName = $task;
    }

    public function forUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function run()
    {
        RunSyncTask::dispatchSync($this->getSyncTask());
    }

    public function getSyncTask(): SyncTask
    {
        if(!isset($this->task)) {
            $sync = Sync::factory()->create(['user_id' => isset($this->user) ? $this->user->id : User::factory()->create()->id]);
            $this->task = SyncTask::factory()->create([
                'task_id' => $this->taskName::id(),
                'sync_id' => $sync->id
            ]);
        }
        return $this->task->refresh();

    }

    public function getTask(): Task
    {
        return $this->getSyncTask()->createTaskObject();
    }

    public function assertSuccessful()
    {
        Assert::assertTrue($this->getSyncTask()->status === 'succeeded');
    }

    public function assertMessages(array $messages)
    {
        Assert::assertEquals($messages, $this->getSyncTask()->messages);
    }


}
