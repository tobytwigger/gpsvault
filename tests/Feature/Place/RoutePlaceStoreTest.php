<?php

namespace Tests\Feature\Place;

use App\Models\Route;
use App\Models\Place;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoutePlaceStoreTest extends TestCase
{

    /** @test */
    public function it_attaches_the_route_place(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('route.place.store', [$route]), ['place_id' => $place->id]);
        $response->assertRedirect(route('route.show', $route));

        $this->assertCount(1, $route->places);
        $this->assertDatabaseCount('places', 1);
    }

    /** @test */
    public function it_returns_a_403_if_the_route_is_not_owned_by_you(){
        $this->authenticated();
        $route = Route::factory()->create();
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('route.place.store', [$route]), ['place_id' => $place->id]);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_place_is_already_attached(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $place = Place::factory()->create();
        $route->places()->sync($place);

        $response = $this->post(route('route.place.store', [$route]), ['place_id' => $place->id], []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_an_error_if_the_place_does_not_exist(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('route.place.store', $route), ['place_id' => 55505]);
        $response->assertSessionHasErrors(['place_id' => 'The selected place id is invalid.']);
    }

    /** @test */
    public function it_returns_a_404_if_the_route_does_not_exist(){
        $this->authenticated();
        $place = Place::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('route.place.store', 55555), ['place_id' => $place->id]);
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $route = Route::factory()->create();
        $place = Place::factory()->create();

        $this->post(route('route.place.store', [$route]), ['place_id' => $place->id])
            ->assertRedirect(route('login'));
    }

}
