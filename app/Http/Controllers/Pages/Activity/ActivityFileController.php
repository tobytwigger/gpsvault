<?php

namespace App\Http\Controllers\Pages\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\File;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\Filepond\FilepondRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityFileController extends Controller
{
    public function destroy(Activity $activity, File $file)
    {
        $this->authorize('view', $activity);
        $this->authorize('delete', $file);
        abort_if(!$activity->files()->where('files.id', $file->id)->exists(), 404, 'The file is not attached to the activity');

        $file->delete();

        return redirect()->route('activity.show', $activity);
    }

    public function store(Request $request, Activity $activity)
    {
        $this->authorize('create', File::class);
        $this->authorize('update', $activity);

        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => [app(FilepondRule::class)],
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535',
        ]);

        $importer = ActivityImporter::update($activity);
        $files = collect($request->input('files', []))
            ->map(function (array $file) use ($request) {
                $file = Upload::filePondFile($file, Auth::user(), FileUploader::ACTIVITY_MEDIA);
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
        $this->authorize('update', $activity);
        abort_if(!$activity->files()->where('files.id', $file->id)->exists(), 404, 'The file is not attached to the activity');

        $request->validate([
            'title' => 'sometimes|nullable|string|max:255',
            'caption' => 'sometimes|nullable|string|max:65535',
        ]);

        $file->title = $request->get('title', $file->title);
        $file->caption = $request->get('caption', $file->caption);

        $file->save();

        return redirect()->route('activity.show', $activity);
    }
}
