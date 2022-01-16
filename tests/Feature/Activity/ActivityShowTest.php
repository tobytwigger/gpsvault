<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use Inertia\Testing\Assert;
use Tests\TestCase;

class ActivityShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_activity(){
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        $this->get(route('activity.show', $activity))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Show')
                ->has('activity', fn (Assert $page) => $page
                    ->where('id', $activity->id)
                    ->etc()
                )
            );
    }

    /** @test */
    public function it_returns_a_403_if_the_activity_is_not_owned_by_you(){
        $this->authenticated();
        $activity = Activity::factory()->create();

        $this->get(route('activity.show', $activity))
            ->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $activity = Activity::factory()->create();
        $this->get(route('activity.show', $activity))
            ->assertRedirect(route('login'));
    }

}
