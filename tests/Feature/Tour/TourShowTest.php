<?php

namespace Tests\Feature\Tour;

use App\Models\Tour;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TourShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_tour()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);

        $this->get(route('tour.show', $tour))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Tour/Show')
                    ->has(
                        'tour',
                        fn (Assert $page) => $page
                    ->where('id', $tour->id)
                    ->etc()
                    )
            );
    }

    /** @test */
    public function it_returns_a_403_if_the_tour_is_not_owned_by_you()
    {
        $this->authenticated();
        $tour = Tour::factory()->create();

        $this->get(route('tour.show', $tour))
            ->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $tour = Tour::factory()->create();
        $this->get(route('tour.show', $tour))
            ->assertRedirect(route('login'));
    }
}
