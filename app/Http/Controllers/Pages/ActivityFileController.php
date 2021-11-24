<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityFileController extends Controller
{

    public function download(Activity $activity, File $file)
    {
        return Storage::download($file->path);
    }

    public function destroy(Activity $activity, File $file)
    {
        $file->delete();

        return redirect()->route('activity.show', $activity);
    }

    public function update(Request $request, Activity $activity, File $file)
    {
        $request->validate([
            'title' => 'sometimes|nullable|string|min:1|max:254',
            'caption' => 'sometimes|nullable|string|min:1|max:60000'
        ]);

        $file->title = $request->get('title', $file->title);
        $file->caption = $request->get('caption', $file->caption);

        $file->save();

        return redirect()->route('activity.show', $activity);
    }
}
