<?php

namespace App\Integrations\Strava\Import\Resources;

use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Integrations\Strava\Jobs\LoadStravaComments;
use App\Integrations\Strava\Jobs\LoadStravaKudos;
use App\Integrations\Strava\Jobs\LoadStravaPhotos;
use App\Integrations\Strava\Jobs\LoadStravaStats;
use App\Integrations\Strava\Models\StravaComment;
use App\Models\Activity as ActivityModel;
use App\Models\Stats;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class Comment
{

    public function import(array $commentData, \App\Models\Activity $activity): Comment
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
