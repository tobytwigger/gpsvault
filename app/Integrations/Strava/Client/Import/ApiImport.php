<?php

namespace App\Integrations\Strava\Client\Import;

use App\Integrations\Strava\Client\Import\Resources\Activity;

class ApiImport
{

    public static function activity(): Activity
    {
        return app(Activity::class);
    }

}
