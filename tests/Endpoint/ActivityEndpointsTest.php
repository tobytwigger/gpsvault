<?php

namespace Tests\Endpoint;

use App\Models\Activity;
use Inertia\Testing\Assert;
use Tests\TestCase;

class ActivityEndpointsTest extends TestCase
{

    /** @test */
    public function index_loads_activities(){
        Activity::factory()->count(5)->create();

        $this->get(route('activity.index'))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Index')
                ->has('activities')
            );
    }

}
