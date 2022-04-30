<?php

namespace App\Http\Controllers\Pages\Activity;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Activity/Index', [
            'activities' => Auth::user()->activities()
                ->orderByStat('started_at')
                ->paginate(request()->input('perPage', 8)),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreActivityRequest $request
     * @return RedirectResponse
     */
    public function store(StoreActivityRequest $request)
    {
        $file = Upload::uploadedFile($request->file('file'), Auth::user(), FileUploader::ACTIVITY_FILE);

        $activity = ActivityImporter::for(Auth::user())
            ->withName($request->input('name'))
            ->withActivityFile($file)
            ->import();

        $activity->analyse();

        return redirect()->route('activity.show', $activity);
    }

    /**
     * Display the specified resource.
     *
     * @param Activity $activity
     * @return \Inertia\Response
     */
    public function show(Activity $activity)
    {
        return Inertia::render('Activity/Show', [
            'activity' => $activity->load(['files', 'stats']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateActivityRequest $request
     * @param Activity $activity
     * @return RedirectResponse
     */
    public function update(UpdateActivityRequest $request, Activity $activity, FileUploader $fileUploader)
    {
        $importer = ActivityImporter::update($activity);
        if ($request->has('name')) {
            $importer->withName($request->input('name'));
        }
        if ($request->has('description')) {
            $importer->withDescription($request->input('description'));
        }
        $activity = $importer->save();

        return redirect()->route('activity.show', $activity);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Activity $activity
     * @return Response
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activity.index');
    }
}
