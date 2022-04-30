<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Models\Stats;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class LoadStravaActivity extends StravaActivityBaseJob
{

    /**
     * Execute the job.
     */
    public function handle(Strava $strava)
    {
        $strava->setUserId($this->activity->user_id);
        $stravaActivity = $strava->client($this->stravaClientModel)->getActivity($this->activity->getAdditionalData('strava_id'));

        if (!$this->activity->description) {
            $this->activity->description = $stravaActivity['description'];
        } else {
            $this->activity->description = PHP_EOL . PHP_EOL . 'Imported from Strava: ' . PHP_EOL . $stravaActivity['description'];
        }

        $stats = $this->activity->statsFrom('strava')->first();
        if ($stats) {
            $stats->average_heartrate = $stravaActivity['average_heartrate'] ?? null;
            $stats->max_heartrate = $stravaActivity['max_heartrate'] ?? null;
            $stats->calories = $stravaActivity['calories'] ?? null;
            $stats->save();
        } else {
            Stats::create([
                'integration' => 'strava',
                'stats_id' => $this->activity->id,
                'stats_type' => Activity::class,
                'average_heartrate' => $stravaActivity['average_heartrate'] ?? null,
                'max_heartrate' => $stravaActivity['max_heartrate'] ?? null,
                'calories' => $stravaActivity['calories'] ?? null,
                'distance' => $stravaActivity['distance'] ?? null,
                'started_at' => isset($stravaActivity['start_date']) ? Carbon::make($stravaActivity['start_date']) : null,
                'duration' => $stravaActivity['elapsed_time'] ?? null,
                'average_speed' => $stravaActivity['average_speed'] ?? null,
                'min_altitude' => $stravaActivity['elev_low'] ?? null,
                'max_altitude' => $stravaActivity['elev_high'] ?? null,
                'elevation_gain' => $stravaActivity['total_elevation_gain'] ?? null,
                'moving_time' => $stravaActivity['moving_time'] ?? null,
                'max_speed' => $stravaActivity['max_speed'] ?? null,
                'average_cadence' => $stravaActivity['average_cadence'] ?? null,
                'average_temp' => $stravaActivity['average_temp'] ?? null,
                'average_watts' => $stravaActivity['average_watts'] ?? null,
                'kilojoules' => $stravaActivity['kilojoules'] ?? null,
                'start_latitude' => Arr::first($stravaActivity['start_latlng'] ?? []),
                'start_longitude' => Arr::last($stravaActivity['start_latlng'] ?? []),
                'end_latitude' => Arr::first($stravaActivity['end_latlng'] ?? []),
                'end_longitude' => Arr::last($stravaActivity['end_latlng'] ?? []),
            ]);
            $this->activity->refresh();
        }
        $this->activity->setAdditionalData('strava_is_loading_details', false);

        LoadStravaStats::dispatch($this->activity);
    }
}
