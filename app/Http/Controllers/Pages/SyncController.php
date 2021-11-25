<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Jobs\RunSyncTask;
use App\Models\Sync;
use App\Services\Sync\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $tasks->each(fn(Task $task) => RunSyncTask::dispatch($task::class, $sync));

        return redirect()->route('sync.index');
    }

    public function index()
    {
    if(request()->get('showSync')) {
    dd(request()->get('showSync'));
    }
        return Inertia::render('Sync/Index', [
            'integrations' => collect(app()->tagged('integrations')),
            'taskDetails' => collect(app()->tagged('tasks'))->map(fn(Task $task) => ['id' => $task::class, 'name' => $task->name(), 'description' => $task->description()]),
            'current' => Auth::user()->syncs()->where('finished', false)->where('cancelled', false)->orderBy('created_at', 'DESC')->first(),
            'previous' => Auth::user()->syncs()->where('finished', true)->orWhere('cancelled', true)->lastFive()->get(),
            'userId' => Auth::id()
        ]);
    }

    public function cancel()
    {
        $sync = Auth::user()->syncs()->where('finished', false)->where('cancelled', false)->orderBy('created_at', 'DESC')->first();

        if($sync === null) {
            throw new NotFoundHttpException('Sync is not currently running');
        }

        $sync->cancelled = true;
        $sync->save();

        return redirect()->route('sync.index');
    }

}
