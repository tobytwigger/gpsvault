<?php

namespace App\Integrations\Strava\Jobs;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Concerns\Trackable;

abstract class StravaBaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public Activity $activity;

    protected $backoff = [60, 120, 180, 300];

//    public function uniqueId()
//    {
//        return $this->activity->id;
//    }

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

    abstract public function alias(): ?string;
}
