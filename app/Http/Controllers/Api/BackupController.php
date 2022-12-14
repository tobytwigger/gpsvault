<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{

    public function index()
    {
        return File::where('type', FileUploader::ARCHIVE)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'DESC')
            ->paginate(request()->input('perPage', 8));
    }

}
