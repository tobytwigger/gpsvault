<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\ApiImport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;

class SyncActivities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function alias(): ?string
    {
        return 'sync-activities';
    }

    public function tags(): array
    {
        return [
            'user_id' => $this->user->id
        ];
    }

    public static function canSeeTracking($user = null, array $tags = []): bool
    {
        return $user?->id === $tags['user_id'];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $strava = Strava::client($this->user);
        $page = 1;
        do {
            $activities = $strava->activity()->getActivities($page);

            if (count($activities) === 0) {
                continue;
            }

            foreach ($activities as $activityData) {
                ApiImport::limitedActivity()->import($activityData, $this->user);
            }

            $page += 1;
        } while (count($activities) > 0) ;
    }
}
