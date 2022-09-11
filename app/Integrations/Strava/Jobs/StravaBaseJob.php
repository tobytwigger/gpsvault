<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Exceptions\StravaRateLimited;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;
use Throwable;

class StravaBaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public Activity $activity;

    public $backoff = [5, 10, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(Activity $activity)
    {
        $this->queue = 'indexing';
        $this->activity = $activity;
    }

    public function tags(): array
    {
        return [
            'activity_id' => $this->activity->id,
        ];
    }

    public static function canSeeTracking(User $user = null, array $tags = []): bool
    {
        try {
            return $user && Activity::findOrFail($tags['activity_id'])->user_id === $user->id;
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    public function retryUntil()
    {
        return now()->addDays(3);
    }

    public function failed(Throwable $e)
    {
        \Illuminate\Support\Facades\Log::error($e->getMessage());
        if ($e instanceof StravaRateLimited) {
            $time = Carbon::now()->addMinutes(15 - (Carbon::now()->minute % 15))
                ->seconds(0);
            $this->release(Carbon::now()->diffInSeconds($time));
        } elseif($e instanceof ClientNotAvailable) {
            $this->release(300);
        }
    }


}
