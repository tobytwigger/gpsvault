<?php

namespace App\Exceptions;

use App\Models\Activity;

class ActivityDuplicate extends \Exception
{
    public Activity $activity;

    public function __construct(Activity $activity)
    {
        parent::__construct('The activity is duplicated by activity #' . $activity->id . '.', 400);

        $this->activity = $activity;
    }
}
