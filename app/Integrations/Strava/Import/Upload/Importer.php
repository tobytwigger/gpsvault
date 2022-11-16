<?php

namespace App\Integrations\Strava\Import\Upload;

use Illuminate\Support\Facades\Facade;

class Importer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return StravaImporter::class;
    }
}
