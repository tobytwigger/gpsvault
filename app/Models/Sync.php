<?php

namespace App\Models;

use App\Services\Sync\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Sync extends Model
{
    use HasFactory;

    protected $fillable = [
        'tasks', 'successful_tasks', 'failed_tasks', 'user_id', 'status', 'finished'
    ];

    protected $casts = [
        'tasks' => 'array',
        'successful_tasks' => 'array',
        'failed_tasks' => 'array',
        'finished' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(fn(Sync $sync) => $sync->user_id = $sync->user_id ?? Auth::id());
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
        ]);
    }

    public function markTaskSuccessful(string $taskClass)
    {
        if(!in_array($taskClass, $this->successful_tasks)) {
            $this->successful_tasks = array_merge($this->successful_tasks, [$taskClass]);
            $this->save();
        }
    }

    public function markTaskFailed(string $taskClass, string $error)
    {
        $this->failed_tasks = array_merge($this->failed_tasks, [$taskClass => $error]);
        $this->save();
    }

}
