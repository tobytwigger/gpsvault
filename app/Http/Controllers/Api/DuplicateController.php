<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DuplicateController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'hash' => 'required|string'
        ]);
        $activity = Activity::whereHas('activityFile',
            fn(Builder $query) => $query->where('hash', $request->input('hash'))->where('type', FileUploader::ACTIVITY_FILE)
        )->first();

        return [
            'is_duplicate' => $activity !== null,
            'file' => $activity?->activityFile,
            'activity' => $activity
        ];
    }

}
