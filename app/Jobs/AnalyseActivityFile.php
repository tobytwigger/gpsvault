<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\File;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Parser\Point;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Bus\Queueable;
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
        $analysis = Analyser::analyse($this->activity);

        ActivityStats::updateOrCreate(
            ['integration' => 'php', 'activity_id' => $this->activity->id],
            [
                'distance' => $analysis->getDistance(),
                'averageSpeed' => $analysis->getAverageSpeed(),
                'averagePace' => $analysis->getAveragePace(),
                'minAltitude' => $analysis->getMinAltitude(),
                'maxAltitude' => $analysis->getMaxAltitude(),
                'cumulativeElevationGain' => $analysis->getCumulativeElevationGain(),
                'cumulativeElevationLoss' => $analysis->getCumulativeElevationLoss(),
                'startedAt' => $analysis->getStartedAt(),
                'finishedAt' => $analysis->getFinishedAt(),
                'duration' => $analysis->getDuration(),
                'average_heartrate' => $analysis->getAverageHeartrate(),
                'maxHeartrate' => $analysis->getMaxHeartrate(),
                'calories' => $analysis->getCalories(),
                'movingTime' => $analysis->getMovingTime(),
                'maxSpeed' => $analysis->getMaxSpeed(),
                'averageCadence' => $analysis->getAverageCadence(),
                'averageTemp' => $analysis->getAverageTemp(),
                'averageWatts' => $analysis->getAverageWatts(),
                'kilojoules' => $analysis->getKilojoules(),
                'start_latitude' => $analysis->getStartLatitude(),
                'start_longitude' => $analysis->getStartLongitude(),
                'end_latitude' => $analysis->getEndLatitude(),
                'end_longitude' => $analysis->getEndLongitude(),

                'distance' => $analysis->getDistance(),
                'average_speed' => $analysis->getAverageSpeed(),
                'average_pace' => $analysis->getAveragePace(),
                'duration' => $analysis->getDuration(),
                'min_altitude' => $analysis->getMinAltitude(),
                'max_altitude' => $analysis->getMaxAltitude(),
                'elevation_gain' => $analysis->getCumulativeElevationGain(),
                'elevation_loss' => $analysis->getCumulativeElevationLoss(),
                'moving_time' => $analysis->getDuration(),
                'started_at' => $analysis->getStartedAt(),
                'finished_at' => $analysis->getFinishedAt(),
                'json_points_file_id' => $this->convertPointsToJsonPath($analysis->getPoints())->id
            ]
        );
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

        $filename = Str::random(40) . '.json.gz';
        return Upload::withContents(gzcompress($json, 9), $filename, $this->activity->user, FileUploader::ACTIVITY_FILE_POINT_JSON);
    }

}
