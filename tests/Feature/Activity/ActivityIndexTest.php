<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use App\Models\User;
use Inertia\Testing\Assert;
use Tests\TestCase;

class ActivityIndexTest extends TestCase
{

    /** @test */
    public function index_loads_activities(){
        $this->be($user = User::factory()->create());
        $activities = Activity::factory()->count(5)->create(['user_id' => $user->id]);

        $this->get(route('activity.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Index')
                ->has('activities', fn (Assert $page) => $page
                    ->has('data', 5)
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_paginates_activities(){

    }

    /** @test */
    public function index_only_returns_your_activities(){

    }

}
