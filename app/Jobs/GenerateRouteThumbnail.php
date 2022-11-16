<?php

namespace App\Jobs;

use App\Models\RoutePath;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\StaticMapGenerator\StaticMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class GenerateRouteThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private RoutePath $routePath;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(RoutePath $routePath)
    {
        $this->routePath = $routePath;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        $file = Upload::withContents(
            StaticMap::ofPath($this->routePath->linestring),
            Uuid::uuid4()->toString() . '_thumbnail.png',
            $this->routePath->route->user,
            FileUploader::MAP_THUMBNAIL
        );
        $this->routePath->thumbnail_id = $file->id;
        $this->routePath->save();
    }
}
