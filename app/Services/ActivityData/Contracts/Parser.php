<?php

namespace App\Services\ActivityData\Contracts;

use App\Models\Activity;
use App\Services\ActivityData\Analysis;

interface Parser
{

    public function analyse(Activity $activity): Analysis;

}
