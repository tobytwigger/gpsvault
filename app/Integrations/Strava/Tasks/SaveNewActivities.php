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
        $client = $this->strava->client($this->user()->id);
        $page = 1;
        do {
            $activities = $client->getActivities($page);
            $page = $page + 1;
            foreach($activities as $activity) {
                if(isset($activity['id'])) {
                    if(!Activity::whereAdditionalDataContains('strava_id', $activity['id'])->exists()) {
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
    }
}
