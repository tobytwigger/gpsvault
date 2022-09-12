<?php

namespace App\Integrations\Strava\Import\Resources;

use App\Integrations\Strava\Models\StravaComment;
use App\Models\Activity as ActivityModel;
use Carbon\Carbon;

class Comment
{
    public function import(array $commentData, ActivityModel $activity): Comment
    {
        StravaComment::updateOrCreate(
            ['strava_id' => $commentData['id']],
            [
                'first_name' => $commentData['athlete']['firstname'],
                'last_name' => $commentData['athlete']['lastname'],
                'activity_id' => $activity->id,
                'text' => $commentData['text'],
                'posted_at' => Carbon::make($commentData['created_at']),
            ]
        );

        return $this;
    }
}
