<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\File;
use App\Models\Sync;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\ActivityImport\Importer;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\Sync\Task;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Activity::class, 'activity');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Activity/Index', [
            'activities' => Activity::where('user_id', Auth::id())
                ->orderBy('started_at', 'DESC')
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Activity/Create', [
            'integrations' => collect(app()->tagged('integrations'))
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreActivityRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreActivityRequest $request)
    {
        $file = Upload::uploadedFile($request->file('file'), Auth::user(), FileUploader::ACTIVITY_FILE);

        $activity = ActivityImporter::for(Auth::user())
            ->withName($request->input('name'))
            ->withActivityFile($file)
            ->withoutDuplicateChecking()
            ->import();

        return redirect()->route('activity.show', $activity);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Inertia\Response
     */
    public function show(Activity $activity)
    {
        return Inertia::render('Activity/Show', [
            'activity' => $activity->load(['files'])->append('stats')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActivityRequest  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateActivityRequest $request, Activity $activity, FileUploader $fileUploader)
    {
        $importer = ActivityImporter::update($activity);

        if($request->hasFile('file')) {
            $file = Upload::uploadedFile($request->file('file'), Auth::user(), FileUploader::ACTIVITY_FILE);
            $importer->withActivityFile($file);
        }

        if($request->has('files')) {
            $files = collect($request->file('files', []))
                ->map(fn(UploadedFile $uploadedFile) => Upload::uploadedFile($uploadedFile, Auth::user(), FileUploader::ACTIVITY_MEDIA));
            $importer->addMedia($files->all());
        }

        $activity = $importer->save();
        return redirect()->route('activity.show', $activity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activity.index');
    }

}
