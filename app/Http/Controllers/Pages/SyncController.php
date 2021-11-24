<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Jobs\RunSyncTask;
use App\Models\Sync;
use App\Services\Sync\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SyncController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function sync()
    {
        $tasks = collect(app()->tagged('tasks'));

        $sync = Sync::start($tasks);

        $tasks->each(fn(Task $task) => RunSyncTask::dispatch($task, $sync));

        return redirect()->route('sync.index');
    }

    public function index()
    {
        return Inertia::render('Sync/Index', [
            'integrations' => collect(app()->tagged('integrations')),
            'sync' => [
                'tasks' => collect(app()->tagged('tasks'))->map(fn(Task $task) => ['id' => $task::class, 'name' => $task->name(), 'description' => $task->description()]),
                'sync' => Auth::user()->syncs()->where('finished', false)->orderBy('created_at', 'DESC')->first(),
                'lastComplete' => null,
                'openSync' => session()->has('withSync')
            ]
        ]);
    }

}
