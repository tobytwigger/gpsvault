<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use App\Models\File;
use Tests\TestCase;

class PlaceDestroyTest extends TestCase
{
    /** @test */
    public function it_deletes_the_place()
    {
        $this->authenticated();
        $place = Place::factory()->create(['user_id' => $this->user->id]);
        $this->assertDatabaseHas('places', ['id' => $place->id]);

        $response = $this->delete(route('place.destroy', $place));
        $response->assertRedirect(route('place.index'));

        $this->assertDatabaseMissing('places', ['id' => $place->id]);
    }

    /** @test */
    public function it_returns_a_403_if_the_place_is_not_owned_by_you()
    {
        $this->authenticated();
        $place = Place::factory()->create();

        $response = $this->delete(route('place.destroy', $place));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $place = Place::factory()->create();

        $this->delete(route('place.destroy', $place))
            ->assertRedirect(route('login'));
    }

}
