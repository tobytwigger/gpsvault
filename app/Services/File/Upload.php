<?php

namespace App\Services\File;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Facade;

/**
 * @method static File withContents(string $contents, string $filename, User $User, string $type) Create a file from the given contents.
 * @method static File uploadedFile(UploadedFile $uploadedFile, User $User, string $type) Create a file from an uploaded file.
 */
class Upload extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FileUploader::class;
    }
}
