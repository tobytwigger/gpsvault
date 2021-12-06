<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\ActivityStatsPoint;
use App\Models\File;
use App\Services\ActivityData\ActivityData;
use App\Services\ActivityData\Point;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class AnalyseActivityFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Activity $activity;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
        $this->queue = 'stats';
        $this->activity = $activity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $analysis = ActivityData::analyse($this->activity);

        ActivityStats::create([
            'distance' => $analysis->getDistance(),
            'average_speed' => $analysis->getAverageSpeed(),
            'average_pace' => $analysis->getAveragePace(),
            'min_altitude' => $analysis->getMinAltitude(),
            'max_altitude' => $analysis->getMaxAltitude(),
            'elevation_gain' => $analysis->getCumulativeElevationGain(),
            'elevation_loss' => $analysis->getCumulativeElevationLoss(),
            'moving_time' => $analysis->getDuration(),
            'started_at' => $analysis->getStartedAt(),
            'finished_at' => $analysis->getFinishedAt(),
            'polyline' => $analysis->getPolyline(),
            'activity_id' => $this->activity->id,
            'integration' => 'php',
            'json_points_file_id' => $this->convertPointsToJsonPath($analysis->getPoints())->id
        ]);
    }

    /**
     * @param array|Point[] $points
     * @return File
     */
    private function convertPointsToJsonPath(array $points): File
    {
        $array = [];
        foreach($points as $point) {
            $array[] = $point->toArray();
        }
        $json = json_encode($array);

        $filename = Str::random(40) . '.json';
        return Upload::withContents($json, $filename, $this->activity->user, FileUploader::ACTIVITY_FILE_POINT_JSON);
    }

}
