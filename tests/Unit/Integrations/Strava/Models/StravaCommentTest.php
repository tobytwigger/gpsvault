<?php

namespace Unit\Integrations\Strava\Models;

use App\Integrations\Strava\Models\StravaComment;
use App\Models\Activity;
use Tests\TestCase;

class StravaCommentTest extends TestCase
{
    /** @test */
    public function it_has_a_name_attribute()
    {
        $comment = StravaComment::factory()->create(['first_name' => 'Toby', 'last_name' => 'T']);
        $this->assertEquals('Toby T', $comment->name);
    }

    /** @test */
    public function it_belongs_to_an_activity()
    {
        $activity = Activity::factory()->create();
        $comment = StravaComment::factory()->create(['activity_id' => $activity->id]);

        $this->assertTrue($activity->is($comment->activity));
    }
}
