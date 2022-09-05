<?php

namespace Feature\Route\Planner;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlannerCreateTest extends TestCase
{
    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('planner.create'))->assertRedirect(route('login'));
    }

    /** @test */
    public function it_loads_the_correct_page()
    {
        $this->authenticated();

        $this->get(route('planner.create'))->assertInertia(fn (Assert $page) => $page->component('Route/Planner'));
    }
}
