<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlaceShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_place()
    {
        $this->authenticated();
        $place = Place::factory()->create();

        $this->get(route('place.show', $place))
            ->assertInertia(
                fn (Assert $page) => $page
            ->component('Place/Show')
            ->has(
                'place',
                fn (Assert $page) => $page
            ->where('id', $place->id)
            ->etc()
            )
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $place = Place::factory()->create();
        $this->get(route('place.show', $place))
            ->assertRedirect(route('login'));
    }
}
