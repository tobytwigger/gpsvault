<?php

namespace App\Models;

use App\Events\SyncFinished;
use App\Events\SyncUpdated;
use App\Services\Sync\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Sync extends Model
{
    use HasFactory;

    protected $fillable = [
        'tasks', 'successful_tasks', 'failed_tasks', 'user_id', 'finished', 'cancelled', 'task_messages'
    ];

    protected $casts = [
        'tasks' => 'array',
        'successful_tasks' => 'array',
        'failed_tasks' => 'array',
        'task_messages' => 'array',
        'finished' => 'boolean',
        'cancelled' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(fn(Sync $sync) => $sync->user_id = $sync->user_id ?? Auth::id());
        static::saving(function(Sync $sync) {
            if($sync->isDirty(['successful_tasks', 'failed_tasks', 'task_messages'])) {
                SyncUpdated::dispatch($sync);
            }
        });
        static::saved(function(Sync $sync) {
            if(
                (count($sync->tasks ?? []) === count($sync->failed_tasks ?? []) + count($sync->successful_tasks ?? []))
                && $sync->finished === false
            ) {
                $sync->finished = true;
                $sync->save();
                SyncFinished::dispatch($sync);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function start(\Illuminate\Support\Collection $tasks): Sync
    {
        return Sync::create([
            'tasks' => $tasks->map(fn(Task $task) => $task::id()),
            'successful_tasks' => [],
            'failed_tasks' => [],
            'task_messages' => []
        ]);
    }

    public function markTaskSuccessful(string $taskId)
    {
        $instance = Sync::sharedLock()->findOrFail($this->id);
        if(!in_array($taskId, $instance->successful_tasks)) {
            $instance->successful_tasks = array_merge($instance->successful_tasks, [$taskId]);
            $instance->save();
        }
    }

    public function markTaskFailed(string $taskId, string $error)
    {
        $instance = Sync::sharedLock()->findOrFail($this->id);
        if(!in_array($taskId, $instance->failed_tasks)) {
            $instance->failed_tasks = array_merge($instance->failed_tasks, [$taskId]);
            $instance->save();
        }
        $this->updateTaskMessage($taskId, $error);
    }

    public function updateTaskMessage(string $taskId, string $message)
    {
        $instance = Sync::sharedLock()->findOrFail($this->id);
        $instance->task_messages = array_merge($instance->task_messages, [$taskId => $message]);
        $instance->save();
    }

    public function scopeLastFive(Builder $query)
    {
        $query->latest()->limit(5);
    }

}
