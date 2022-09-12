<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\StaticMapGenerator\StaticMap;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateActivityThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Stats $stats;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        $model = $this->stats->model;
        if ($model instanceof Activity && $model->thumbnail_id === null) {
            $file = Upload::withContents(
                StaticMap::ofPath($this->stats->linestring),
                'thumbnail.png',
                $model->user,
                FileUploader::MAP_THUMBNAIL
            );
            $model->thumbnail_id = $file->id;
            $model->save();
        }
    }
}
