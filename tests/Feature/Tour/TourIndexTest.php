<?php

namespace Tests\Feature\Tour;

use App\Models\Tour;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TourIndexTest extends TestCase
{
    /** @test */
    public function index_loads_right_component()
    {
        $this->authenticated();

        $this->get(route('tour.index'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Tour/Index')
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('tour.index'))->assertRedirect(route('login'));
    }
}
