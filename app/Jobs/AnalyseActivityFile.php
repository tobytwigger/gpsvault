<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Services\ActivityData\ActivityData;
use App\Services\ActivityImport\ActivityImporter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
            'activity_id' => $this->activity->id,
            'integration' => 'php'
        ]);
    }
}
