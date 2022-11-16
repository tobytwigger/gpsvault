<?php

namespace App\Integrations\Strava\Import\Api\Resources;

use App\Models\Activity as ActivityModel;
use Illuminate\Support\Arr;

class Photos
{
    public function import(array $photoData, ActivityModel $activity): Photos
    {
        if (isset($photoData['unique_id']) && !collect(Arr::wrap($activity->getAdditionalData('strava_photo_ids')))->contains($photoData['unique_id'])) {
            $activity->pushToAdditionalDataArray('strava_photo_ids', $photoData['unique_id']);
        }

        return $this;
    }
}
