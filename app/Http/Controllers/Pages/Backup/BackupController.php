<?php

namespace App\Http\Controllers\Pages\Backup;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\Sync\Sync;
use App\Services\File\FileUploader;
use App\Tasks\CreateBackupTask;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BackupController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(File::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Backups/Index', [
            'backups' => File::where('type', FileUploader::ARCHIVE)
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'DESC')
                ->paginate(request()->input('perPage', 8)),
            'task' => Auth::user()->syncs()
                ->whereHas('pendingTasks', fn(Builder $query) => $query->where('task_id', CreateBackupTask::id()))
                ->first()
                ?->tasks()->where('task_id', CreateBackupTask::id())->first()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(
            Auth::user()->syncs()->whereHas('pendingTasks', fn(Builder $query) => $query->where('task_id', CreateBackupTask::id()))->exists(),
            400, 'A backup is already being generated'
        );

        $sync = Sync::start();
        $sync->withTask(app(CreateBackupTask::class), []);
        $sync->refresh()->dispatch();

        return redirect()->route('backup.index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, File $file)
    {
        $request->validate([
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535',
        ]);

        $file->title = $request->input('title', $file->title);
        $file->caption = $request->input('caption', $file->caption);

        $file->save();

        return redirect()->route('backup.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(File $file)
    {
        $file->delete();

        return redirect()->route('backup.index');
    }

}
