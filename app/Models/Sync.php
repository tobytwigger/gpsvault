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

class Sync extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $casts = [];

    protected static function booted()
    {
        static::creating(fn(Sync $sync) => $sync->user_id = $sync->user_id ?? Auth::id());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param \Illuminate\Support\Collection|Task[] $tasks
     * @return Sync
     */
    public static function start(): Sync
    {
        return Sync::create();
    }

    public function tasks()
    {
        return $this->hasMany(SyncTask::class);
    }

    public function withTask(Task $task, array $config): SYnc
    {
        $task = SyncTask::newTask($task, $this, $config);
        return $this;
    }

    public function scopeLastFive(Builder $query)
    {
        $query->latest()->limit(5);
    }

    public function dispatch()
    {
        $this->tasks->each(fn(SyncTask $task) => $task->dispatch());
    }

}
