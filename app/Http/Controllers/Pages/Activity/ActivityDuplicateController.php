<?php

namespace App\Http\Controllers\Pages\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityDuplicateController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('create', Activity::class);

        $request->validate([
            'hash' => 'required|string'
        ]);
        $activity = Activity::where('user_id', Auth::id())->whereHas('activityFile',
            fn(Builder $query) => $query->where('hash', $request->input('hash'))->where('type', FileUploader::ACTIVITY_FILE)
        )->first();

        return [
            'is_duplicate' => $activity !== null,
            'file' => $activity?->activityFile,
            'activity' => $activity
        ];
    }

}
