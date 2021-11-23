<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class SaveNewStravaActivities implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(?User $user = null)
    {
        $this->user = $user ?? Auth::user();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Strava $strava)
    {
        $client = $strava->client($this->user->id);
        $page = 1;
        do {
            $activities = $client->getActivities($page);
            $page = $page + 1;
            foreach($activities as $activity) {
                if(!Activity::whereAdditionalDataContains('strava_id', $activity['id'])->exists()) {
                    Activity::create([
                        'name' => $activity['name'],
                        'distance' => $activity['distance'],
                        'start_at' => Carbon::make($activity['start_date'])
                    ]);
                }
            }
        } while(count($activities) > 0);

    }
}
