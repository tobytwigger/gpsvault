<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlaceIndexTest extends TestCase
{
    /** @test */
    public function index_loads_the_component()
    {
        $this->authenticated();

        $this->get(route('place.index'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Place/Index')
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('place.index'))->assertRedirect(route('login'));
    }
}
