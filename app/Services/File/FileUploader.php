<?php

namespace App\Services\File;

use App\Models\File;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{
    public const ACTIVITY_FILE = 'activity_file';

    public const ACTIVITY_MEDIA = 'activity_media';

    public const MAP_THUMBNAIL = 'map_thumbnail';

    public const ARCHIVE = 'archive';

    public const ACTIVITY_FILE_POINT_JSON = 'activity_file_point_json';

    public const ROUTE_FILE_POINT_JSON = 'route_file_point_json';

    public const ROUTE_FILE = 'route_file';

    public const ROUTE_MEDIA = 'route_media';

    public function withContents(string $contents, string $filename, User $user, string $type): File
    {
        $path = sprintf('%s/%s', $type, $filename);
        Storage::disk($user->disk())->put($path, $contents);

        $extension = Str::of($filename)->after('.');
        $mimetype = MimeType::fromExtension($extension)
            ?? config('app.mimetypes.' . $extension)
            ?? throw new Exception(sprintf('Extension [%s] is not supported', $extension));

        return File::create([
            'path' => $path,
            'filename' => $filename,
            'size' => Storage::disk($user->disk())->size($path),
            'mimetype' => $mimetype,
            'extension' => $extension,
            'disk' => $user->disk(),
            'user_id' => $user->id,
            'type' => $type,
        ]);
    }

    public function uploadedFile(UploadedFile $uploadedFile, User $user, string $type): File
    {
        $path = $uploadedFile->store('activities', $user->disk());

        $file = File::create([
            'path' => $path,
            'filename' => $uploadedFile->getClientOriginalName(),
            'size' => Storage::disk($user->disk())->size($path),
            'mimetype' => $uploadedFile->getClientMimeType(),
            'extension' => $uploadedFile->getClientOriginalExtension(),
            'disk' => $user->disk(),
            'user_id' => $user->id,
            'type' => $type,
        ]);

        if ($uploadedFile->getClientOriginalExtension() === 'tcx') {
            Storage::disk($file->disk)->update($file->path, trim($file->getFileContents()));
        }

        return $file;
    }
}
