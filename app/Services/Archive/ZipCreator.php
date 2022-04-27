<?php

namespace App\Services\Archive;

use App\Models\User;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ZipCreatorFactory start(?User $user = null)
 */
class ZipCreator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ZipCreatorFactory::class;
    }
}
