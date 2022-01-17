<?php

namespace App\Jobs;

use App\Models\ActivityStats;
use App\Models\File;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\File\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public File $file;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(File $file)
    {
        $this->queue = 'stats';
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->file->fileFile) {
            throw new NotFoundHttpException(sprintf('File %u does not have a file associated with it', $this->file->id));
        }
        $analysis = Analyser::analyse($this->file->fileFile);

        ActivityStats::updateOrCreate(
            ['integration' => 'php', 'activity_id' => $this->file->id],
            [
                'distance' => $analysis->getDistance(),
                'average_speed' => $analysis->getAverageSpeed(),
                'average_pace' => $analysis->getAveragePace(),
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
                'json_points_file_id' => Upload::filePoints($analysis->getPoints(), $this->file->user)->id
            ]
        );
    }

    public function middleware()
    {
        return [
            (new WithoutOverlapping(static::class))->releaseAfter(5)
        ];
    }

}
