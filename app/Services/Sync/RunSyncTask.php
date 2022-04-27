<?php

namespace App\Services\Sync;

use App\Exceptions\TaskCancelled;
use App\Exceptions\TaskSucceeded;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunSyncTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public SyncTask $task;

    protected $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(SyncTask $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        if ($this->task->status === 'queued') {
            $this->task->setStatusAsProcessing();

            try {
                $this->task->createTaskObject()->process($this->task);
            } catch (TaskSucceeded $e) {
                $this->succeedTask($e->getMessage());

                return;
            } catch (TaskCancelled $e) {
                $this->cancelTask($e->getMessage());

                return;
            } catch (\Throwable $e) {
                $this->failTask($e->getMessage());

                throw $e;
            }
            $this->task->setStatusAsSucceeded();
        }
    }

    private function succeedTask(?string $message = null)
    {
        if ($message !== null) {
            $this->task->addMessage($message);
        }
        $this->task->setStatusAsSucceeded();
    }

    private function failTask(?string $message = null)
    {
        if ($message !== null) {
            $this->task->addMessage($message);
        }
        $this->task->setStatusAsFailed();
    }

    private function cancelTask(?string $message = null)
    {
        if ($message !== null) {
            $this->task->addMessage($message);
        }
        $this->task->setStatusAsCancelled();
    }

    public function retryUntil()
    {
        return now()->addMinutes(360);
    }
}
