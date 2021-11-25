<?php

namespace App\Jobs;

use App\Models\Sync;
use App\Models\User;
use App\Services\Sync\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunSyncTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $taskId;

    public Sync $sync;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $taskId, Sync $sync)
    {
        $this->taskId = $taskId;
        $this->sync = $sync;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app($this->taskId)->process($this->sync);
        $this->sync->markTaskSuccessful($this->taskId);
    }

    public function failed(\Throwable $exception)
    {
        $this->sync->markTaskFailed($this->taskId, $exception->getMessage());
    }
}
