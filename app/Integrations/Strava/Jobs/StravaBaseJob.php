<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Exceptions\StravaRateLimited;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;
use Throwable;

abstract class StravaBaseJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public Activity $activity;

    protected $backoff = [60, 120, 180, 300];

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    public function uniqueId()
    {
        return $this->activity->id;
    }

//    public $backoff = [1, 3, 5, 10, 15];

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
            'user_id' => $this->activity->user_id,
        ];
    }

    public static function canSeeTracking(User $user = null, array $tags = []): bool
    {
        try {
            return $user && $tags['user_id'] === $user->id;
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
//        if ($e instanceof StravaRateLimited) {
//            $time = Carbon::now()->addMinutes(15 - (Carbon::now()->minute % 15))
//                ->seconds(0);
//            $this->release(Carbon::now()->diffInSeconds($time));
//            return;
//        } elseif ($e instanceof ClientNotAvailable) {
//            $this->release(180);
//            return;
//        }
//        throw $e;
    }

    abstract public function alias(): ?string;

    public function middleware()
    {
        // Throttle exceptions
        return [(new ThrottlesExceptions(1, 1))->by($this->alias() . '--' . $this->activity->id)];
    }
}
