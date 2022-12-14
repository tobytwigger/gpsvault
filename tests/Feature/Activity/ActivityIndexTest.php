<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use App\Models\Stats;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ActivityIndexTest extends TestCase
{
    /** @test */
    public function index_loads_component()
    {
        $this->authenticated();

        $this->get(route('activity.index'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Activity/Index')
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('activity.index'))->assertRedirect(route('login'));
    }
}
