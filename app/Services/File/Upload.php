<?php

namespace App\Services\File;

use App\Models\File;
use App\Models\User;
use App\Services\Analysis\Parser\Point;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;

/**
 * @method static File withContents(string $contents, string $filename, User $User, string $type) Create a file from the given contents.
 * @method static File uploadedFile(UploadedFile $uploadedFile, User $User, string $type) Create a file from an uploaded file.
 * @method static File activityPoints(array|Point[] $points, User $User) Create a file from a set of activity points.
 */
class Upload extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return FileUploader::class;
    }

}
