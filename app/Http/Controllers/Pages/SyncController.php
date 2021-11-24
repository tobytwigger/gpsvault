<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Jobs\RunSyncTask;
use App\Models\Sync;
use App\Services\Sync\Task;

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

        return redirect()->route('activity.create')->with('withSync', true);
    }

}
