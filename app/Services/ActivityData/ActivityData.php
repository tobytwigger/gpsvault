<?php

namespace App\Services\ActivityData;

use App\Models\Activity;
use App\Services\ActivityData\Contracts\Parser;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Parser parser(string $type) Get the file parser for the given file extension
 * @method static void registerCustomParser(string $type, \Closure $creator) Register a function to create a new parser for the given type
 * @method static Analysis analyse(Activity $activity) Analyse the given activity file
 */
class ActivityData extends Facade
{

    protected static function getFacadeAccessor()
    {
        return \App\Services\ActivityData\Contracts\ActivityDataFactory::class;
    }

}
