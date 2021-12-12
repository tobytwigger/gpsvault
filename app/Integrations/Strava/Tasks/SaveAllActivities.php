<?php

namespace App\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Import\ImportStravaActivity;
use App\Integrations\Strava\Client\Strava;
use App\Models\User;
use App\Services\Sync\Task;

class SaveAllActivities extends Task
{

    private Strava $strava;

    private int $totalActivities = 0;

    private int $newActivites = 0;

    private int $updatedActivities = 0;

    public function __construct(Strava $strava)
    {
        $this->strava = $strava;
    }

    public function description(): string
    {
        return 'Save any new or updated Strava activities';
    }

    public function name(): string
    {
        return 'Import activities from Strava';
    }

    public function disableBecause(User $user): ?string
    {
        if (!$user->stravaTokens()->exists()) {
            return 'Your account must be connected to Strava';
        }
        return null;
    }

    public function run()
    {
        $client = $this->strava->setUserId($this->user()->id)->client();
        $page = 1;
        do {
            $this->line(sprintf('Collecting activities %u to %u', ($page - 1) * 50, $page * 50));
            $activities = $client->getActivities($page);

            $this->totalActivities = $this->totalActivities + count($activities);
            foreach ($activities as $activityData) {
                $importer = ImportStravaActivity::importFromApi($activityData, $this->user());
                if($importer->wasCreated()) {
                    $this->newActivites++;
                } elseif($importer->wasUpdated()) {
                    $this->updatedActivities++;
                }
            }
            $this->offerBail(sprintf('Cancelled after %u activities, added %u and updated %u.', $this->totalActivities, $this->newActivites, $this->updatedActivities));

            $page = $page + 1;
        } while (count($activities) > 0);

        $this->line(sprintf('Found %u activities, including %u new and %u updated.', $this->totalActivities, $this->newActivites, $this->updatedActivities));
    }



}
