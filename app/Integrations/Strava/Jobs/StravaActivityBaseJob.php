<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Exceptions\StravaRateLimited;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class StravaActivityBaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Activity $activity;

    public $backoff = [5, 10, 60];

    protected StravaClient $stravaClientModel;

    /**
     * Create a new job instance.
     */
    public function __construct(Activity $activity)
    {
        $this->queue = 'indexing';
        $this->activity = $activity;
        $this->stravaClientModel = $this->activity->user->availableClient();
    }

    public function retryUntil()
    {
        return now()->addDays(3);
    }

    public function failed(Throwable $e)
    {
        if ($e instanceof StravaRateLimited) {
            $time = Carbon::now()->addMinutes(15 - (Carbon::now()->minute % 15))
                ->seconds(0);

            $this->release(Carbon::now()->diffInSeconds($time));
        }
    }
}
