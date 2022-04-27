<?php

namespace Tests\Feature\Tour;

use App\Models\Tour;
use Tests\TestCase;

class TourDestroyTest extends TestCase
{

    /** @test */
    public function it_deletes_the_tour()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $this->assertDatabaseHas('tours', ['id' => $tour->id]);

        $response = $this->delete(route('tour.destroy', $tour));
        $response->assertRedirect(route('tour.index'));

        $this->assertDatabaseMissing('tours', ['id' => $tour->id]);
    }

    /** @test */
    public function it_returns_a_403_if_the_tour_is_not_owned_by_you()
    {
        $this->authenticated();
        $tour = Tour::factory()->create();

        $response = $this->delete(route('tour.destroy', $tour));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $tour = Tour::factory()->create();

        $this->delete(route('tour.destroy', $tour))
            ->assertRedirect(route('login'));
    }
}
