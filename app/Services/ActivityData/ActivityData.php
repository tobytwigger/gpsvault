<?php

namespace App\Services\ActivityData;

use Illuminate\Support\Facades\Facade;

class ActivityData extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\Services\ActivityData\Contracts\ActivityDataFactory::class;
    }

}
