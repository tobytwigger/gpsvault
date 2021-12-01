<?php

namespace App\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\Sync\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaveNewActivities extends Task
{

    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->strava = $strava;
    }

    public function description(): string
    {
        return 'Save any new Strava activities and associated information';
    }

    public function name(): string
    {
        return 'Save new Strava activities';
    }

    public function disableBecause(User $user): ?string
    {
        if(!$user->stravaTokens()->exists()) {
            return 'Your account must be connected to Strava';
        }
        return null;
    }

    public function run()
    {
        $client = $this->strava->setUserId($this->user()->id)->client();
        $page = 1;
        $analysisCount = 0;
        $newCount = 0;
        do {
            $this->line(sprintf('Collecting activities %u to %u', ($page - 1) * 50, $page * 50));
            $activities = $client->getActivities($page);
            $page = $page + 1;
            $analysisCount = $analysisCount + count($activities);
            foreach($activities as $activity) {
                if(isset($activity['id'])) {
                    if(!Activity::whereAdditionalDataContains('strava_id', $activity['id'])->exists()) {
                        $newCount++;
                        /** @var Activity $activity */
                        ActivityImporter::for($this->user())
                            ->withName($activity['name'])
                            ->withDistance($activity['distance'])
                            ->linkTo('strava')
                            ->startedAt(Carbon::make($activity['start_date']))
                            ->withAdditionalData('strava_id', $activity['id'])
                            ->withAdditionalData('upload_id', $activity['upload_id_str'] ?? null)
                            ->import();
                    }
                }
            }
            $this->offerBail(sprintf('Cancelled with %u new tasks added.', $newCount));
        } while(count($activities) > 0);

        $this->line(sprintf('Found %u activities, including %u new.', $analysisCount, $newCount));
    }
}
