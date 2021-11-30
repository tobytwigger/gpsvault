<?php

namespace App\Services\Archive;

use Illuminate\Support\Facades\Facade;

class ZipCreator extends Facade
{

    protected static function getFacadeAccessor()
    {
        return ZipCreatorFactory::class;
    }

}
