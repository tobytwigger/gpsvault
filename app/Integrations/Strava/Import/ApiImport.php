<?php

namespace App\Integrations\Strava\Import;

use App\Integrations\Strava\Import\Resources\Activity;
use App\Integrations\Strava\Import\Resources\Comment;
use App\Integrations\Strava\Import\Resources\Kudos;
use App\Integrations\Strava\Import\Resources\LimitedActivity;
use App\Integrations\Strava\Import\Resources\Photos;
use App\Integrations\Strava\Import\Resources\Stats;

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
