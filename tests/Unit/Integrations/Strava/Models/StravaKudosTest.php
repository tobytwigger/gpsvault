<?php

namespace Unit\Integrations\Strava\Models;

use App\Integrations\Strava\Models\StravaKudos;
use App\Models\Activity;
use Tests\TestCase;

class StravaKudosTest extends TestCase
{
    /** @test */
    public function it_has_a_name_attribute()
    {
        $kudos = StravaKudos::factory()->create(['first_name' => 'Toby', 'last_name' => 'T']);
        $this->assertEquals('Toby T', $kudos->name);
    }

    /** @test */
    public function it_belongs_to_an_activity()
    {
        $activity = Activity::factory()->create();
        $kudos = StravaKudos::factory()->create(['activity_id' => $activity->id]);

        $this->assertTrue($activity->is($kudos->activity));
    }
}
