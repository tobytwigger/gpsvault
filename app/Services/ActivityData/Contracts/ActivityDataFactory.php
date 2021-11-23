<?php

namespace App\Services\ActivityData\Contracts;

use App\Models\Activity;
use App\Services\ActivityData\Analysis;

interface ActivityDataFactory
{

    public function analyse(Activity $activity): Analysis;

    public function parser(string $type): Parser;

    public function registerCustomParser(string $type, \Closure $creator);


}
