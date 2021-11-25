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
            if($sync->isDirty(['finished']) && $sync->finished === true) {
                SyncFinished::dispatch($sync);
            }
        });
        static::saving(function(Sync $sync) {
            if(count($sync->tasks ?? []) === count($sync->failed_tasks ?? []) + count($sync->successful_tasks ?? [])) {
                $sync->finished = true;
            } else {
                $sync->finished = false;
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
            'tasks' => $tasks->map(fn(Task $task) => $task::class),
            'successful_tasks' => [],
            'failed_tasks' => [],
            'task_messages' => []
        ]);
    }

    public function markTaskSuccessful(string $taskClass)
    {
        Log::info(sprintf('Passed task %s', $taskClass));
        if(!in_array($taskClass, $this->successful_tasks)) {
            $this->successful_tasks = array_merge($this->successful_tasks, [$taskClass]);
            $this->save();
        }
    }

    public function markTaskFailed(string $taskClass, string $error)
    {
        Log::info(sprintf('Failed task %s', $taskClass));
        if(!in_array($taskClass, $this->failed_tasks)) {
            $this->failed_tasks = array_merge($this->failed_tasks, [$taskClass]);
            $this->save();
        }
        $this->updateTaskMessage($taskClass, $error);
    }

    public function updateTaskMessage(string $taskClass, string $message)
    {
        Log::info(sprintf('Message for %s: %s', $taskClass, $message));
        $this->task_messages = array_merge($this->task_messages, [$taskClass => $message]);
        $this->save();
    }

    public function scopeLastFive(Builder $query)
    {
        $query->latest()->limit(5);
    }

}
