<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\File;
use App\Models\Sync;
use App\Services\Sync\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Activity/Index', [
            'activities' => Activity::orderBy('start_at', 'DESC')->get()
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
            'integrations' => collect(app()->tagged('integrations')),
            'sync' => [
                'tasks' => collect(app()->tagged('tasks'))->map(fn(Task $task) => ['id' => $task::class, 'name' => $task->name(), 'description' => $task->description()]),
                'sync' => Auth::user()->syncs()->where('finished', false)->orderBy('created_at', 'DESC')->first(),
                'lastComplete' => null,
                'openSync' => session()->has('withSync')
            ]
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
        $path = $request->file('file')->store('activities');

        $file = File::create([
            'path' => $path,
            'filename' => $request->file('file')->getClientOriginalName(),
            'size' => Storage::size($path),
            'mimetype' => $request->file('file')->getClientMimeType(),
            'extension' => $request->file('file')->getClientOriginalExtension()
        ]);

        $activity = Activity::create([
            'name' => $request->input('name'),
            'activity_file_id' => $file->id,
            'type' => $request->file('file')->clientExtension()
        ]);

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
            'activity' => $activity->load('files')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateActivityRequest  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        if(!$activity->hasActivityFile() && $request->hasFile('file')) {
            $path = $request->file('file')->store('activities');
            $file = File::create([
                'path' => $path,
                'filename' => $request->file('file')->getClientOriginalName(),
                'size' => Storage::size($path),
                'mimetype' => $request->file('file')->getClientMimeType(),
                'extension' => $request->file('file')->getClientOriginalExtension()
            ]);

            $activity->activity_file_id = $file->id;
        }

        if($request->has('files')) {
            foreach($request->file('files', []) as $uploadedFile) {
                $path = $uploadedFile->store('media');

                $file = File::create([
                    'path' => $path,
                    'filename' => $uploadedFile->getClientOriginalName(),
                    'size' => Storage::size($path),
                    'mimetype' => $uploadedFile->getClientMimeType(),
                    'extension' => $uploadedFile->getClientOriginalExtension()
                ]);

                $activity->files()->attach($file);
            }
        }

        $activity->save();

        return redirect()->route('activity.show', $activity->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        //
    }

    public function download(Activity $activity)
    {
        if($activity->activityFile) {
            return Storage::download($activity->activityFile->getFullPath());
        }
        throw new NotFoundHttpException('This activity does not have a file associated with it');
    }
}
