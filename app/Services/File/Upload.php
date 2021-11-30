<?php

namespace App\Services\File;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Storage;

class Upload extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return FileUploader::class;
    }

}
