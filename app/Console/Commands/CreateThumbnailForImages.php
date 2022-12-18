<?php

namespace App\Console\Commands;

use App\Jobs\CreateThumbnailImage;
use App\Models\File;
use App\Services\File\FileUploader;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CreateThumbnailForImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbnail:generate
                            {--regenerate : If given, all existing thumbnails will be regenerated and replaced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate thumbnails for all image files missing them';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $images = File::where('mimetype', 'LIKE', '%image%')
            ->whereNot('type', FileUploader::IMAGE_THUMBNAIL)
            ->when(!$this->option('regenerate'), fn (Builder $query) => $query->whereNull('thumbnail_id'))
            ->get();

        $this->line(sprintf('Generating %u thumbnails.', $images->count()));

        foreach ($images as $image) {
            CreateThumbnailImage::dispatch($image);
        }

        return Command::SUCCESS;
    }
}
