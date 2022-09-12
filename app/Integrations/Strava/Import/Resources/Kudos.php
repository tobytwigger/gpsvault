<?php

namespace App\Integrations\Strava\Import\Resources;

use App\Integrations\Strava\Models\StravaKudos;

class Kudos
{
    public function import(array $kudosData, \App\Models\Activity $activity): Kudos
    {
        StravaKudos::updateOrCreate([
            'first_name' => $kudosData['firstname'],
            'last_name' => $kudosData['lastname'],
            'activity_id' => $activity->id,
        ], []);

        return $this;
    }
}
