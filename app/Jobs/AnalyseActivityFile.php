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
        $stats = Stats::updateOrCreate(
            ['integration' => 'php', 'stats_id' => $this->activity->id, 'stats_type' => get_class($this->activity)],
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
                'start_point' => new \MStaack\LaravelPostgis\Geometries\Point($analysis->getStartLatitude(), $analysis->getStartLongitude()),
                'end_point' => new \MStaack\LaravelPostgis\Geometries\Point($analysis->getEndLatitude(), $analysis->getEndLongitude()),
                'min_altitude' => $analysis->getMinAltitude(),
                'max_altitude' => $analysis->getMaxAltitude(),
                'started_at' => $analysis->getStartedAt(),
                'finished_at' => $analysis->getFinishedAt(),
            ]
        );

        $this->saveLinestring($stats, $analysis->getPoints());

        $this->savePoints($stats, $analysis->getPoints());

        $this->status()->successMessage('Saved data');
    }

    private function savePoints(Stats $stats, array $points)
    {
        $stats->activityPoints()->delete();

        $activityPoints = collect($points)->chunk(1000);
        $percentage = 0;
        $increase = 100 / ($activityPoints->count() < 1 ? 1 : $activityPoints->count());
        $order = 0;

        foreach ($activityPoints as $chunkedPoints) {
            $stats->activityPoints()->createMany(collect($chunkedPoints)->map(function (Point $point) use (&$order) {
                $toReturn = [
                    'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
                    'elevation' => $point->getElevation(),
                    'time' => $point->getTime(),
                    'cadence' => $point->getCadence(),
                    'temperature' => $point->getTemperature(),
                    'heart_rate' => $point->getHeartRate(),
                    'speed' => $point->getSpeed(),
                    'grade' => $point->getGrade(),
                    'order' => $order,
                    'battery' => $point->getBattery(),
                    'calories' => $point->getCalories(),
                    'cumulative_distance' => $point->getCumulativeDistance(),
                ];
                $order += 1;

                return $toReturn;
            }));

            $percentage += $increase;
            $this->status()->setPercentage($percentage);
        }
    }

//    public function middleware()
//    {
//        return [
//            (new WithoutOverlapping('FileAnalyser')),
//        ];
//    }

    /**
     * @param Stats $stats
     * @param array|Point[] $points
     */
    private function saveLinestring(Stats $stats, array $points)
    {
        $convertedPoints = [];

        foreach ($points as $point) {
            if ($point->getLatitude() && $point->getLongitude() && $point->getElevation()) {
                $convertedPoints[] = new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude(), $point->getElevation());
            }
        }

        if (count($convertedPoints) > 1) {
            $stats->linestring = new LineString($convertedPoints);
            $stats->save();
        }
    }
}
