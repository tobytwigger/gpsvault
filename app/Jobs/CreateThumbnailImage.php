<?php

namespace App\Jobs;

use App\Models\File;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class CreateThumbnailImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public File $file;

    public int $width;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(File $file, int $width = 344)
    {
        $this->file = $file;
        $this->width = $width;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        // Save the file into a temporary file
        $temporaryShortPath = 'optimization/' . $this->file->id . '.' . $this->file->extension;
        $temporaryOptimisedShortPath = 'optimization/' . $this->file->id . '_optimized.' . $this->file->extension;
        Storage::disk('temp')->put($temporaryShortPath, $this->file->getFileContents());
        $temporaryLongPath = Storage::disk('temp')->path($temporaryShortPath);
        $temporaryOptimisedLongPath = Str::replace($temporaryShortPath, $temporaryOptimisedShortPath, $temporaryLongPath);

        Image::load($temporaryLongPath)
            ->fit(Manipulations::FIT_MAX, $this->width, $this->width)
            ->quality(0)
            ->optimize()
            ->save($temporaryOptimisedLongPath);

        $compressedFile = Upload::withContents(
            (string) Storage::disk('temp')->get($temporaryOptimisedShortPath),
            Uuid::uuid4()->toString() . '_thumbnail_compressed.png',
            $this->file->user,
            FileUploader::IMAGE_THUMBNAIL
        );

        Storage::disk('temp')->delete($temporaryShortPath);
        Storage::disk('temp')->delete($temporaryOptimisedShortPath);

        $compressedFile->title = $this->file->title;
        $compressedFile->caption = $this->file->caption;
        $compressedFile->save();

        if($this->file->thumbnail_id !== null) {
            $this->file->thumbnail()->delete();
        }
        $this->file->thumbnail_id = $compressedFile->id;
        $this->file->save();
    }
}
