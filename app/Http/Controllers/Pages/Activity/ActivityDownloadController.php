<?php

namespace App\Http\Controllers\Pages\Activity;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Services\Archive\ZipCreator;

class ActivityDownloadController extends Controller
{
    public function downloadActivity(Activity $activity)
    {
        $this->authorize('view', $activity);

        $file = ZipCreator::add($activity)->archive();

        return redirect()->route('file.download', $file);
    }
}
