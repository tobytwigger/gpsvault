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
use Ramsey\Uuid\Uuid;

class CreateThumbnailImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public File $file;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        $image = \Intervention\Image\Facades\Image::make($this->file->getFileContents())
            ->resize(344, null, fn ($constraint) => $constraint->aspectRatio());

        $compressedFile = Upload::withContents(
            (string) $image->encode(),
            Uuid::uuid4()->toString() . '_thumbnail_compressed.png',
            $this->file->user,
            FileUploader::IMAGE_THUMBNAIL
        );
        $compressedFile->title = $this->file->title;
        $compressedFile->caption = $this->file->caption;
        $compressedFile->save();

        $this->file->thumbnail_id = $compressedFile->id;
        $this->file->save();
    }
}
