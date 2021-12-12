<?php

namespace App\Integrations\Strava\Jobs;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class StravaActivityBaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Activity $activity;

    public $backoff = [5, 10, 60];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Activity $activity)
    {
        $this->queue = 'indexing';
        $this->activity = $activity;
    }

    public function middleware()
    {
        return [
            new RateLimited('strava'),
            (new WithoutOverlapping('strava'))->expireAfter(180)
        ];
    }

    public function retryUntil()
    {
        return now()->addDays(3);
    }

    public function failed(\Throwable $e)
    {
        $this->release(900);
    }

}
