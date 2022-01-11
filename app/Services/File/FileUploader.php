<?php

namespace App\Services\File;

use App\Models\File;
use App\Models\User;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploader
{

    const ACTIVITY_FILE = 'activity_file';

    const ACTIVITY_MEDIA = 'activity_media';

    const ARCHIVE = 'archive';

    const UNMATCHED_MEDIA = 'unmatched_media';

    const ACTIVITY_FILE_POINT_JSON = 'activity_file_point_json';

    const ROUTE_FILE = 'route_file';

    const ROUTE_MEDIA = 'route_media';

    public function withContents(string $contents, string $filename, User $user, string $type): File
    {
        $path = sprintf('%s/%s', $type, $filename);
        Storage::disk($user->disk())->put($path, $contents);

        $extension = Str::of($filename)->after('.');
        $mimetype = MimeType::fromExtension($extension)
            ?? config('app.mimetypes.' . $extension)
            ?? throw new \Exception(sprintf('Extension [%s] is not supported', $extension));

        return File::create([
            'path' => $path,
            'filename' => $filename,
            'size' => Storage::disk($user->disk())->size($path),
            'mimetype' => $mimetype,
            'extension' => $extension,
            'disk' => $user->disk(),
            'user_id' => $user->id,
            'type' => $type
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
            'type' => $type
        ]);

        if($uploadedFile->getClientOriginalExtension() === 'tcx') {
            Storage::disk($file->disk)->update($file->path, trim($file->getFileContents()));
        }

        return $file;
    }

    public function activityPoints(array $points, User $user): File
    {
        $array = [];
        foreach($points as $point) {
            $array[] = $point->toArray();
        }
        $json = json_encode($array);

        $filename = Str::random(40) . '.json.gz';
        return $this->withContents(gzcompress($json, 9), $filename, $user, FileUploader::ACTIVITY_FILE_POINT_JSON);
    }

}
