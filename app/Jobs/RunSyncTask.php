<?php

namespace App\Jobs;

use App\Models\Sync;
use App\Models\SyncTask;
use App\Models\User;
use App\Services\Sync\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RunSyncTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private SyncTask $task;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SyncTask $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $taskObject = app('tasks.' . $this->task->taskId());
        $taskObject->setConfig($this->task->config());
        $task->process($this->sync);
        $this->sync->markTaskSuccessful($this->taskId);
    }

    public function failed(\Throwable $exception)
    {
        $this->sync->markTaskFailed($this->taskId, $exception->getMessage());
    }
}
