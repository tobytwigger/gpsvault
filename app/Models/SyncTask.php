<?php

namespace App\Models;

use App\Events\SyncFinished;
use App\Events\SyncUpdated;
use App\Jobs\RunSyncTask;
use App\Services\Sync\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SyncTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id', 'config', 'status', 'messages', 'sync_id'
    ];

    protected $casts = [
        'config' => 'array',
        'messages' => 'array',
    ];

    public function sync()
    {
        return $this->belongsTo(Sync::class);
    }

    public static function newTask(Task $task, Sync $sync, array $config = [])
    {
        return Task::create([
            'task_id' => $task::id(),
            'sync_id' => $sync->id,
            'messages' => ['Added to queue'],
            'config' => $config
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setStatusAsFailed()
    {
        $this->status = 'failed';
        $this->save();
    }

    public function setStatusAsSucceeded()
    {
        $this->status = 'succeeded';
        $this->save();
    }

    public function setStatusAsProcessing()
    {
        $this->status = 'processing';
        $this->save();
    }

    public function setStatusAsCancelled()
    {
        $this->status = 'cancelled';
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
