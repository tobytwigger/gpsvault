<?php

namespace App\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Services\Sync\Task;
use Carbon\Carbon;

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
                        Activity::create([
                            'name' => $activity['name'],
                            'distance' => $activity['distance'],
                            'start_at' => Carbon::make($activity['start_date']),
                            'linked_to' => ['strava'],
                            'additional_data' => ['strava_id' => $activity['id'], 'upload_id' => $activity['upload_id_str'] ?? null]
                        ]);
                    }
                }
            }
        } while(count($activities) > 0);

        $this->line(sprintf('Found %u activities, including %u new.', $analysisCount, $newCount));
    }
}
