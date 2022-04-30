<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Models\StravaKudos;

class LoadStravaKudos extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     */
    public function handle(Strava $strava)
    {
        $strava->setUserId($this->activity->user_id);
        $page = 1;
        do {
            $kudoses = $strava->client($this->stravaClientModel)->getKudos($this->activity->getAdditionalData('strava_id'), $page);
            foreach ($kudoses as $kudos) {
                $this->importKudos($kudos);
            }
            $page++;
        } while (count($kudoses) === 200);

        $this->activity->setAdditionalData('strava_is_loading_kudos', false);
    }

    private function importKudos(mixed $kudos)
    {
        StravaKudos::updateOrCreate([
            'first_name' => $kudos['firstname'],
            'last_name' => $kudos['lastname'],
            'activity_id' => $this->activity->id,
        ], []);
    }
}
