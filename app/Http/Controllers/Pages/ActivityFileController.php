<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\File;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityFileController extends Controller
{

    public function destroy(Activity $activity, File $file)
    {
        $this->authorize('view', $file);

        if(!(
            $activity->activity_file_id === $file->id
            || $activity->whereHas('files', fn(Builder $query) => $query->where('files.id', $file->id))->exists()
        )) {
            throw new NotFoundHttpException('The file is not attached to the activity');
        }

        $file->delete();

        return redirect()->route('activity.show', $activity);
    }

    public function store(Request $request, Activity $activity)
    {
        $this->authorize('create', File::class);

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:10000',
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535'
        ]);

        $importer = ActivityImporter::update($activity);
        $files = collect($request->file('files', []))
            ->map(function(UploadedFile $uploadedFile) use ($request) {
                $file = Upload::uploadedFile($uploadedFile, Auth::user(), FileUploader::ACTIVITY_MEDIA);
                $file->title = $request->input('title');
                $file->caption = $request->input('caption');
                $file->save();
                return $file;
            });
        $importer->addMedia($files->all());
        $importer->save();

        return redirect()->route('activity.show', $activity);
    }

    public function update(Request $request, Activity $activity, File $file)
    {
        $this->authorize('update', $file);

        $request->validate([
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535'
        ]);

        $file->title = $request->get('title', $file->title);
        $file->caption = $request->get('caption', $file->caption);

        $file->save();

        return redirect()->route('activity.show', $activity);
    }
}
