<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Parser\Point;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Concerns\Trackable;
use MStaack\LaravelPostgis\Geometries\LineString;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnalyseActivityFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public Activity $activity;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(Activity $activity)
    {
        $this->queue = 'stats';
        $this->activity = $activity;
    }

    public function users(): array
    {
        return [$this->activity->user_id];
    }

    public function tags(): array
    {
        return [
            'activity_id' => $this->activity->id,
        ];
    }

    public function alias(): string
    {
        return 'analyse-activity-file';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if (!$this->activity->hasFile()) {
            throw new NotFoundHttpException(sprintf('Activity %u does not have a file associated with it.', $this->activity->id));
        }

        $this->status()->line('Starting analysis');
        $analysis = Analyser::analyse($this->activity->file);
        $this->status()->successMessage('Analysis finished');

        $this->status()->line('Saving data');

        $pointLocationData = collect($analysis->getPoints())
            ->filter(fn (Point $point) => $point->getLatitude() && $point->getLongitude())
            ->map(fn (Point $point) => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude(), $point->getElevation() ?? 0))
            ->toArray();

        $stats = Stats::updateOrCreate(
            ['integration' => 'php', 'activity_id' => $this->activity->id],
            [
                'linestring' => count($pointLocationData) > 1 ? new LineString($pointLocationData) : null,
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
                'start_point' => new \MStaack\LaravelPostgis\Geometries\Point($analysis->getStartLatitude(), $analysis->getStartLongitude()),
                'end_point' => new \MStaack\LaravelPostgis\Geometries\Point($analysis->getEndLatitude(), $analysis->getEndLongitude()),
                'min_altitude' => $analysis->getMinAltitude(),
                'max_altitude' => $analysis->getMaxAltitude(),
                'started_at' => $analysis->getStartedAt(),
                'finished_at' => $analysis->getFinishedAt(),
                'time_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getTime())->all(),
                'cadence_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getCadence())->all(),
                'temperature_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getTemperature())->all(),
                'heart_rate_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getHeartRate())->all(),
                'speed_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getSpeed())->all(),
                'grade_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getGrade())->all(),
                'battery_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getBattery())->all(),
                'calories_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getCalories())->all(),
                'cumulative_distance_data' => collect($analysis->getPoints())->map(fn (Point $point) => $point->getCumulativeDistance())->all(),
            ]
        );

        $this->status()->successMessage('Saved data');
    }

//    public function middleware()
//    {
//        return [
//            (new WithoutOverlapping('FileAnalyser')),
//        ];
//    }
}
