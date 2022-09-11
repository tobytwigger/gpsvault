<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\ApiImport;
use App\Integrations\Strava\Models\StravaKudos;

class LoadStravaKudos extends StravaBaseJob
{
    public function alias(): ?string
    {
        return 'load-strava-kudos';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $strava = Strava::client($this->activity->user);
        $page = 1;
        do {
            $kudoses = $strava->activity()->getKudos($this->activity->getAdditionalData('strava_id'), $page);
            foreach ($kudoses as $kudos) {
                ApiImport::kudos()->import($kudos, $this->activity);
            }
            $page++;
        } while (count($kudoses) === 200);
    }

}
