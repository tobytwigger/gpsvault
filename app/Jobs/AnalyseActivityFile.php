<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\File;
use App\Models\User;
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
                'average_speed' => $analysis->getAverageSpeed(),
                'average_pace' => $analysis->getAveragePace(),
                'minAltitude' => $analysis->getMinAltitude(),
                'maxAltitude' => $analysis->getMaxAltitude(),
                'elevation_gain' => $analysis->getCumulativeElevationGain(),
                'elevation_loss' => $analysis->getCumulativeElevationLoss(),
                'startedAt' => $analysis->getStartedAt(),
                'finishedAt' => $analysis->getFinishedAt(),
                'duration' => $analysis->getDuration(),
                'average_heartrate' => $analysis->getAverageHeartrate(),
                'max_heartrate' => $analysis->getMaxHeartrate(),
                'calories' => $analysis->getCalories(),
                'moving_time' => $analysis->getMovingTime(),
                'max_speed' => $analysis->getMaxSpeed(),
                'average_cadence' => $analysis->getAverageCadence(),
                'average_temp' => $analysis->getAverageTemp(),
                'average_watts' => $analysis->getAverageWatts(),
                'kilojoules' => $analysis->getKilojoules(),
                'start_latitude' => $analysis->getStartLatitude(),
                'start_longitude' => $analysis->getStartLongitude(),
                'end_latitude' => $analysis->getEndLatitude(),
                'end_longitude' => $analysis->getEndLongitude(),
                'min_altitude' => $analysis->getMinAltitude(),
                'max_altitude' => $analysis->getMaxAltitude(),
                'started_at' => $analysis->getStartedAt(),
                'finished_at' => $analysis->getFinishedAt(),
                'json_points_file_id' => Upload::activityPoints($analysis->getPoints(), $this->activity->user)->id
            ]
        );
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addDay();
    }

}
