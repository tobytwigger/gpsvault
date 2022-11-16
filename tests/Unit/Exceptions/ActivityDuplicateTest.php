<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\ActivityDuplicate;
use App\Models\Activity;
use Tests\TestCase;

class ActivityDuplicateTest extends TestCase
{
    /** @test */
    public function it_sets_the_message()
    {
        $activity = Activity::factory()->create();
        $exception = new ActivityDuplicate($activity);
        $this->assertEquals('The activity is duplicated by activity #' . $activity->id . '.', $exception->getMessage());
    }
}
