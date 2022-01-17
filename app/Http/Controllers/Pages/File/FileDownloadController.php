<?php

namespace App\Http\Controllers\Pages\File;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{

    public function download(File $file)
    {
        $this->authorize('view', $file);

        return $file->returnDownloadResponse();
    }

}
