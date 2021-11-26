<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Jobs\RunSyncTask;
use App\Models\Sync;
use App\Services\Sync\Task;
use Illuminate\Http\Request;
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
    public function sync(Request $request)
    {

        $sync = Sync::start();

        foreach($request->input('tasks', []) as $key => $task) {
            $config = $task['config'] ?? [];
            if($request->file('tasks.' . $key)) {
                $config = array_merge($config, $request->file('tasks.' . $key . '.config'));
            }
            $taskObject = app('tasks.' . $task['id']);
            $sync->withTask($taskObject, $taskObject->processConfig($config));
        }

        $sync->dispatch();

        return redirect()->route('sync.index');
    }

    public function index()
    {
        return Inertia::render('Sync/Index', [
            'integrations' => collect(app()->tagged('integrations')),
            'taskDetails' => collect(app()->tagged('tasks'))->map(fn(Task $task) => $task->toArray()),
            'current' => Auth::user()->syncs()->whereDoesntHave('pendingTasks')->orderBy('created_at', 'DESC')->first(),
            'previous' => Auth::user()->syncs()->whereDoesntHave('pendingTasks')->lastFive()->get(),
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
