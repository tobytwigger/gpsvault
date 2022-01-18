<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stats;
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

    public Activity|Route $model;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Activity|Route $model)
    {
        $this->queue = 'stats';
        $this->model = $model;
    }

    private function getModelName()
    {
        return $this->model instanceof Activity
            ? 'Activity'
            : 'Route';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->model->hasFile()) {
            throw new NotFoundHttpException(sprintf('%s %u does not have a model associated with it.', $this->getModelName(), $this->model->id));
        }
        $analysis = Analyser::analyse($this->model->file);

        Stats::updateOrCreate(
            ['integration' => 'php', 'stats_id' => $this->model->id, 'stats_type' => get_class($this->model)],
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
                'json_points_model_id' => $this->getPointsFileId($analysis->getPoints())
            ]
        );
    }

    private function getPointsFileId(array $points)
    {
        if($this->model instanceof Activity) {
            return Upload::activityPoints(
                $points,
                $this->model->user
            )->id;
        } else {
            return Upload::routePoints(
                $points,
                $this->model->user
            )->id;
        }

    }

    public function middleware()
    {
        return [
            (new WithoutOverlapping(static::class))->releaseAfter(5)
        ];
    }

}
