<?php

namespace App\Integrations\Strava\Import\Api;

use App\Integrations\Strava\Import\Api\Resources\Activity;
use App\Integrations\Strava\Import\Api\Resources\Comment;
use App\Integrations\Strava\Import\Api\Resources\Kudos;
use App\Integrations\Strava\Import\Api\Resources\LimitedActivity;
use App\Integrations\Strava\Import\Api\Resources\Photos;
use App\Integrations\Strava\Import\Api\Resources\Stats;

class ApiImport
{
    public static function activity(): Activity
    {
        return app(Activity::class);
    }

    public static function limitedActivity(): LimitedActivity
    {
        return app(LimitedActivity::class);
    }

    public static function comment(): Comment
    {
        return app(Comment::class);
    }

    public static function kudos(): Kudos
    {
        return app(Kudos::class);
    }

    public static function photos(): Photos
    {
        return app(Photos::class);
    }

    public static function stats(): Stats
    {
        return app(Stats::class);
    }
}
