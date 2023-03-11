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
use JobStatus\Concerns\Trackable;
use Ramsey\Uuid\Uuid;

class GenerateActivityThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private Stats $stats;

    /**
     * Create a new job instance.
     *
     */
    public function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    public function tags()
    {
        return [
            'activity' => $this->stats->model->id,
        ];
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
                Uuid::uuid4()->toString() . '_thumbnail.png',
                $model->user,
                FileUploader::MAP_THUMBNAIL
            );
            $model->thumbnail_id = $file->id;
            $model->save();
        }
    }
}
