<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityFileController extends Controller
{

    public function destroy(Activity $activity, File $file)
    {
        $this->authorize('view', $file);
        abort_if($activity->activity_file_id !== $file->id, 404, 'The file is not attached to the activity');

        $file->delete();

        return redirect()->route('activity.show', $activity);
    }

    public function update(Request $request, Activity $activity, File $file)
    {
        $this->authorize('update', $file);

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
