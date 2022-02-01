<?php

namespace App\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Import\ApiImport;
use App\Integrations\Strava\Client\Import\ImportStravaActivity;
use App\Integrations\Strava\Client\Import\Resources\Activity;
use App\Integrations\Strava\Client\Strava;
use App\Models\User;
use App\Services\Sync\Task;

class SyncLocalActivities extends Task
{

    public function run()
    {
        $strava = Strava::client($this->user());
        $createdActivies = 0;
        $updatedActivities = 0;
        $totalActivities = 0;

        $page = 1;
        do {
            $activities = $strava->activity()->getActivities($page);

            if(count($activities) === 0) {
                continue;
            }

            $this->line(sprintf('Importing activities %u to %u', $totalActivities, $totalActivities + count($activities)));

            $totalActivities = $totalActivities + count($activities);

            foreach ($activities as $activityData) {
                $import = ApiImport::activity()->import($activityData, $this->user());

                match($import->status()) {
                    Activity::CREATED => $createdActivies++,
                    Activity::UPDATED => $updatedActivities++
                };
            }
            $this->offerBail(sprintf('Cancelled after %u activities, added %u and updated %u.', $totalActivities, $createdActivies, $updatedActivities));

            $page = $page + 1;
        } while (count($activities) > 0);

        $this->line(sprintf('Found %u activities, including %u new and %u updated.', $totalActivities, $createdActivies, $updatedActivities));
    }



}
