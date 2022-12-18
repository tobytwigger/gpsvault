<?php

namespace App\Http\Controllers\Pages\File;

use App\Http\Controllers\Controller;
use App\Models\File;

class FilePreviewController extends Controller
{
    public function preview(File $file)
    {
        $this->authorize('view', $file);

        if ($file->thumbnail_id !== null && request()->input('highResolution', false) === false) {
            $file = $file->thumbnail;
        }

        return response($file->getFileContents(), 200)->header('Content-Type', $file->mimetype);
    }
}
