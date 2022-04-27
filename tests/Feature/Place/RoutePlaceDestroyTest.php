<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use App\Models\Route;
use Tests\TestCase;

class RoutePlaceDestroyTest extends TestCase
{

    /** @test */
    public function it_detaches_the_route_place()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $place = Place::factory()->create(['user_id' => $this->user->id]);
        $route->places()->sync($place);

        $response = $this->delete(route('route.place.destroy', [$route, $place]));
        $response->assertRedirect(route('route.show', $route));

        $this->assertCount(0, $route->places);
        $this->assertDatabaseCount('places', 1);
    }

    /** @test */
    public function it_returns_a_403_if_the_route_is_not_owned_by_you()
    {
        $this->authenticated();
        $route = Route::factory()->create();
        $place = Place::factory()->create(['user_id' => $this->user->id]);
        $route->places()->sync($place);

        $response = $this->delete(route('route.place.destroy', [$route, $place]));
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_place_is_not_attached()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $place = Place::factory()->create();

        $response = $this->delete(route('route.place.destroy', [$route, $place]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_place_does_not_exist()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('route.place.destroy', [$route, 55555]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_route_does_not_exist()
    {
        $this->authenticated();
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('route.place.destroy', [55555, $place]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create();
        $place = Place::factory()->create();
        $route->places()->sync($place);

        $this->delete(route('route.place.destroy', [$route, $place]))
            ->assertRedirect(route('login'));
    }
}
