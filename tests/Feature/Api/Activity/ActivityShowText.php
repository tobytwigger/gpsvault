<?php

namespace Tests\Feature\Api\Activity;

use App\Models\Activity;
use App\Models\User;
use Tests\TestCase;

class ActivityShowText extends TestCase
{
    /** @test */
    public function show_returns_the_activity()
    {
        $this->authenticatedWithSanctum();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('api.activity.show', $activity->id));
        $response->assertOk();
        $response->assertJson($activity->toArray());
    }

    /** @test */
    public function it_throws_an_exception_if_you_do_not_own_the_activity()
    {
        $this->authenticatedWithSanctum();
        $activity = Activity::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson(route('api.activity.show', $activity->id));
        $response->assertForbidden();
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.activity.index'))->assertUnauthorized();
    }
}
