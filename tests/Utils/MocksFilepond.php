<?php

namespace Tests\Utils;

use App\Services\Filepond\FilePondFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sopamo\LaravelFilepond\Filepond;

trait MocksFilepond
{
    public function createFile(string $filename, int $size, string $mimetype, ?string $contents = null): FilePondFile
    {
        $path = config('filepond.temporary_files_path', 'filepond') . DIRECTORY_SEPARATOR . Str::random();
        $disk = config('filepond.temporary_files_disk', 'local');

        Storage::disk($disk)->put($path, $contents ?? 'Dummy file');

        return new FilePondFile(
            $filename,
            $size,
            $mimetype,
            app(Filepond::class)->getServerIdFromPath($path),
            Str::afterLast($filename, '.')
        );
    }
}
