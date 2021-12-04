<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Integrations\Strava\Models\StravaKudos;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class IndexStravaActivityKudos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->queue = 'indexing';
        $this->strava = $strava;
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param StravaActivityUpdated $activityEvent
     * @return bool
     */
    public function shouldQueue(StravaActivityUpdated $activityEvent)
    {
        return in_array('strava', $activityEvent->activity->linked_to);
    }

    public function handle(StravaActivityUpdated $activityEvent)
    {
        $activity = $activityEvent->activity->refresh();
        $this->strava->setUserId($activity->user_id);
        $page = 1;
        do {
            $kudoses = $this->strava->client()->getKudos($activity->getAdditionalData('strava_id'), $page);
            foreach($kudoses as $kudos) {
                $this->importKudos($activity, $kudos);
            }
            $page++;
        } while (count($kudoses) === 200);

        $activity->setAdditionalData('strava_is_loading_kudos', false);

    }

    public function middleware()
    {
        return [
            new RateLimited('strava')
        ];
    }

    private function importKudos(Activity $activity, mixed $kudos)
    {
        StravaKudos::updateOrCreate([
            'first_name' => $kudos['firstname'],
            'last_name' => $kudos['lastname'],
            'activity_id' => $activity->id
        ], []);
    }

}
