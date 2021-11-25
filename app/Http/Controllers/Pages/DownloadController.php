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

class DownloadController extends Controller
{

    public function downloadActivityFile(Activity $activity)
    {
        if($activity->activityFile) {
            return Storage::disk($activity->activityFile->disk)->download($activity->activityFile->path, $activity->activityFile->filename);
        }
        throw new NotFoundHttpException('This activity does not have a file associated with it');
    }

    public function downloadActivityMedia(Activity $activity, File $file)
    {
        return Storage::disk($file->disk)->download($file->path, $file->filename);
    }

    public function downloadActivity(Activity $activity)
    {
        dd($activity);
    }
}
