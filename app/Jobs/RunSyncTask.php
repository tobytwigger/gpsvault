<?php

namespace App\Jobs;

use App\Exceptions\TaskCancelled;
use App\Exceptions\TaskSucceeded;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class RunSyncTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const DEPENDENCIES_NOT_READY_MESSAGE = 'Waiting on previous tasks to finish';

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
        if($this->dependenciesNotReady()) {
            if(Arr::last($this->task->messages) !== static::DEPENDENCIES_NOT_READY_MESSAGE) {
                $this->task->addMessage(static::DEPENDENCIES_NOT_READY_MESSAGE);
            }
            RunSyncTask::dispatch($this->task)->delay(5);
            return;
        } else {
            if(Arr::last($this->task->messages) === static::DEPENDENCIES_NOT_READY_MESSAGE) {
                $this->task->addMessage('Task running');
            }
        }
        if($this->task->status === 'queued') {
            $this->task->setStatusAsProcessing();
            try {
                $this->task->createTaskObject()->process($this->task);
            } catch (TaskSucceeded $e) {
                $this->succeedTask($e->getMessage());
                return;
            } catch (TaskCancelled $e) {
                $this->cancelTask($e->getMessage());
                return;
            }
            $this->task->setStatusAsSucceeded();
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->failTask($exception->getMessage());
    }

    private function succeedTask(?string $message = null)
    {
        if($message !== null) {
            $this->task->addMessage($message);
        }
        $this->task->setStatusAsSucceeded();
    }

    private function failTask(?string $message = null)
    {
        if($message !== null) {
            $this->task->addMessage($message);
        }
        $this->task->setStatusAsFailed();
    }

    private function cancelTask(?string $message = null)
    {
        if($message !== null) {
            $this->task->addMessage($message);
        }
        $this->task->setStatusAsCancelled();
    }

    private function dependenciesNotReady(): bool
    {
        return collect($this->task->createTaskObject()->runsAfter())
            // Check if the dependency is a task that hasn't yet finished
            ->filter(fn(string $id) => $this->task->sync->pendingTasks()->where('task_id', $id)->count() > 0)
            ->count() > 0;
    }
}
