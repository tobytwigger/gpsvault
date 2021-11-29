<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncRequest;
use App\Integrations\Strava\Client\Strava;
use App\Jobs\RunSyncTask;
use App\Models\Sync;
use App\Models\SyncTask;
use App\Services\Sync\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SyncController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Sync::class, 'sync');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SyncRequest $request)
    {
        abort_if(Auth::user()->syncs()->whereHas('pendingTasks')->exists(), 400, 'A sync is already running');
        $sync = Sync::start();

        foreach($request->input('tasks', []) as $key => $task) {
            $config = $task['config'] ?? [];
            if($request->file('tasks.' . $key)) {
                $config = array_merge($config, $request->file('tasks.' . $key . '.config'));
            }
            $taskObject = app('tasks.' . $task['id']);
            $sync->withTask($taskObject, $taskObject->processConfig($config));
        }

        $sync->refresh()->dispatch();

        return redirect()->route('sync.index');
    }

    public function index(Strava $strava)
    {
        return Inertia::render('Sync/Index', [
            'integrations' => collect(app()->tagged('integrations')),
            'taskDetails' => collect(app()->tagged('tasks'))->map(fn(Task $task) => $task->toArray()),
            'current' => Auth::user()->syncs()->whereHas('pendingTasks')->orderBy('created_at', 'DESC')->first(),
            'previous' => Auth::user()->syncs()->whereDoesntHave('pendingTasks')->lastFive()->get(),
            'userId' => Auth::id()
        ]);
    }

    public function destroy(Sync $sync)
    {
        $sync->cancel();

        return redirect()->route('sync.index');
    }

}
