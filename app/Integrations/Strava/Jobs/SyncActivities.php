<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Import\ApiImport;
use App\Integrations\Strava\Client\Strava;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncActivities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
                ApiImport::activity()->import($activityData, $this->user);

                $page += 1;
            }
        } while (count($activities) > 0) ;
    }
}
