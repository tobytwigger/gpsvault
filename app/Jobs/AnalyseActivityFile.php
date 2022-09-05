<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stats;
use App\Models\User;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Parser\Point;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseActivityFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public Activity|Route $model;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Activity|Route $model)
    {
        $this->queue = 'stats';
        $this->model = $model;
    }

    public static function canSeeTracking(User $user = null, array $tags = []): bool
    {
        $activity = Activity::findOrFail($tags['activityId'] ?? null);
        if ($user !== null && $activity->user_id === $user->id) {
            return true;
        }

        return false;
    }

    public function tags(): array
    {
        return [
            'activityId' => $this->model->id,
        ];
    }

    public function alias(): string
    {
        return 'analyse-activity';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (!$this->model->hasFile()) {
            throw new NotFoundHttpException(sprintf('Activity %u does not have a model associated with it.', $this->model->id));
        }

        $analysis = Analyser::analyse($this->model->file);

        $stats = Stats::updateOrCreate(
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
            ]
        );

        $this->savePoints($stats, $analysis->getPoints());
    }

    private function savePoints(Stats $stats, array $points)
    {
        $stats->waypoints()->delete();

        foreach (collect($points)->chunk(1000) as $chunkedPoints) {
            $stats->waypoints()->createMany($chunkedPoints->map(fn (Point $point) => [
                'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
                'elevation' => $point->getElevation(),
                'time' => $point->getTime(),
                'cadence' => $point->getCadence(),
                'temperature' => $point->getTemperature(),
                'heart_rate' => $point->getHeartRate(),
                'speed' => $point->getSpeed(),
                'grade' => $point->getGrade(),
                'battery' => $point->getBattery(),
                'calories' => $point->getCalories(),
                'cumulative_distance' => $point->getCumulativeDistance(),
            ]));
        }
    }

    public function middleware()
    {
        return [
            (new WithoutOverlapping('FileAnalyser')),
        ];
    }
}
