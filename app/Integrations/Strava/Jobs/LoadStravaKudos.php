<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Integrations\Strava\Models\StravaKudos;
use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class LoadStravaKudos extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Strava $strava)
    {
        $strava->setUserId($this->activity->user_id);
        $page = 1;
        do {
            $kudoses = $strava->client($this->stravaClientModel)->getKudos($this->activity->getAdditionalData('strava_id'), $page);
            foreach($kudoses as $kudos) {
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
            'activity_id' => $this->activity->id
        ], []);
    }

}
