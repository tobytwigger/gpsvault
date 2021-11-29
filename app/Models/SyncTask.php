<?php

namespace App\Models;

use App\Events\SyncFinished;
use App\Events\SyncUpdated;
use App\Events\TaskUpdated;
use App\Jobs\RunSyncTask;
use App\Services\Sync\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SyncTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'config', 'status', 'messages', 'sync_id', 'started_at', 'finished_at', 'percentage'
    ];

    protected $casts = [
        'config' => 'array',
        'messages' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];

    protected $appends = [
        'runtime', 'latest_message'
    ];

    public function getRuntimeAttribute()
    {
        if($this->started_at && $this->finished_at) {
            return $this->started_at->diffInSeconds($this->finished_at);
        }
        return null;
    }

    public function finished()
    {
        return in_array($this->status, [
            'succeeded', 'failed', 'cancelled'
        ]);
    }

    public function getLatestMessageAttribute(): string
    {
        if(!empty($this->messages)) {
            return Arr::last($this->messages);
        }
        if($this->status === 'failed') {
            return 'Task failed';
        }
        if($this->status === 'cancelled') {
            return 'Task cancelled';
        }
        if($this->status === 'succeeded') {
            return 'Task ran successfully';
        }
        if($this->status === 'queued') {
            return 'Task in queue';
        }
        if($this->status === 'processing') {
            return 'Task running';
        }
        return 'Loading';
    }

    protected static function booted()
    {
        static::saved(function(SyncTask $task) {
            TaskUpdated::dispatch($task);
        });
        static::saved(function(SyncTask $task) {
            if(
                $task->isDirty(['status'])
                && $task->sync->pendingTasks()->count() === 0
                && in_array($task->getOriginal('status'), ['processing', 'queued'])
                && in_array($task->status, ['failed', 'cancelled', 'succeeded'])) {
                $task->sync->finish();
            }
        });
    }

    public static function newTask(Task $task, Sync $sync, array $config = [])
    {
        return SyncTask::create([
            'task_id' => $task::id(),
            'sync_id' => $sync->id,
            'messages' => [],
            'config' => $config
        ]);
    }

    public function sync()
    {
        return $this->belongsTo(Sync::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setStatusAsFailed()
    {
        $this->status = 'failed';
        $this->finished_at = Carbon::now();
        $this->save();
    }

    public function setPercentage(int $percentage)
    {
        $this->percentage = $percentage;
        $this->save();
    }

    public function setStatusAsSucceeded()
    {
        $this->status = 'succeeded';
        $this->finished_at = Carbon::now();
        $this->save();
    }

    public function setStatusAsProcessing()
    {
        $this->status = 'processing';
        $this->started_at = Carbon::now();
        $this->save();
    }

    public function setStatusAsCancelled()
    {
        $this->status = 'cancelled';
        $this->finished_at = Carbon::now();
        $this->save();
    }

    public function addMessage(string $message)
    {
        $this->messages = array_merge($this->refresh()->messages ?? [], [$message]);
        $this->save();
    }

    public function scopeLastFive(Builder $query)
    {
        $query->latest()->limit(5);
    }

    public function dispatch()
    {
        RunSyncTask::dispatch($this);
    }

    public function taskId(): string
    {
        return $this->task_id;
    }

    public function getTaskAttribute(): string
    {
        return $this->createTaskObject()->toArray();
    }

    public function createTaskObject(): Task
    {
        return app('tasks.' . $this->taskId());
    }

    public function config(): array
    {
        return $this->config ?? [];
    }

}
