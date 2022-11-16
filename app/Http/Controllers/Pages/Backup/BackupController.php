<?php

namespace App\Http\Controllers\Pages\Backup;

use App\Http\Controllers\Controller;
use App\Jobs\CreateFullBackup;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use JobStatus\Models\JobStatus;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (JobStatus::forJobAlias('create-full-backup')
            ->whereTag('user_id', Auth::user()->id)
            ->whereNotStatus(['succeeded', 'failed', 'cancelled'])
            ->exists()) {
            throw new HttpException(403, 'A backup is already running');
        }
        CreateFullBackup::dispatch(Auth::user());

        return redirect()->route('backup.index');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param File $file
     * @return RedirectResponse
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
     * @param File $file
     * @return RedirectResponse
     */
    public function destroy(File $file)
    {
        $file->delete();

        return redirect()->route('backup.index');
    }
}
