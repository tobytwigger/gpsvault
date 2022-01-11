<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadAllData;
use App\Models\Activity;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use Illuminate\Support\Facades\Auth;

class ActivityDownloadController extends Controller
{

    public function all()
    {
        DownloadAllData::dispatch(Auth::user());

        return redirect()->route('profile.show');
    }

    public function downloadActivity(Activity $activity)
    {
        $this->authorize('view', $activity);

        $file = ZipCreator::add($activity)->archive();

        return redirect()->route('file.download', $file);
    }

    public function download()
    {

    }
}
