<?php

namespace App\Http\Controllers\Pages\File;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FilePreviewController extends Controller
{

    public function preview(File $file)
    {
        $this->authorize('view', $file);

        return response($file->getFileContents(), 200)->header('Content-Type', $file->mimetype);
    }

}
