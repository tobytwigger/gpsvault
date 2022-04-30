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

class SyncLocalActivities implements ShouldQueue
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
    public function handle(Strava $strava)
    {
        $strava = Strava::client($this->user);
//        $createdActivies = 0;
//        $updatedActivities = 0;
//        $totalActivities = 0;

        $page = 1;
        do {
            $activities = $strava->activity()->getActivities($page);

            if (count($activities) === 0) {
                continue;
            }

//            $this->line(sprintf('Importing activities %u to %u', $totalActivities, $totalActivities + count($activities)));

//            $totalActivities += count($activities);

            foreach ($activities as $activityData) {
                ApiImport::activity()->import($activityData, $this->user);

//                match ($import->status()) {
//                    \App\Integrations\Strava\Client\Import\Resources\Activity::CREATED => $createdActivies++,
//                    Activity::UPDATED => $updatedActivities++
//                };
            }
//            $this->offerBail(sprintf('Cancelled after %u activities, added %u and updated %u.', $totalActivities, $createdActivies, $updatedActivities));

            $page += 1;
        } while (count($activities) > 0);

//        $this->line(sprintf('Found %u activities, including %u new and %u updated.', $totalActivities, $createdActivies, $updatedActivities));
    }
}
