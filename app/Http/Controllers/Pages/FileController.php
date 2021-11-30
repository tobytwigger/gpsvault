<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    public function download(File $file)
    {
        $this->authorize('view', $file);

        return $file->returnDownloadResponse();
    }

}
