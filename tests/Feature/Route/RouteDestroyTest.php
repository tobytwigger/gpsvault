<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use App\Models\File;
use Tests\TestCase;

class RouteDestroyTest extends TestCase
{

    /** @test */
    public function it_deletes_the_route(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $this->assertDatabaseHas('routes', ['id' => $route->id]);

        $response = $this->delete(route('route.destroy', $route));
        $response->assertRedirect(route('route.index'));

        $this->assertDatabaseMissing('routes', ['id' => $route->id]);
    }

    /** @test */
    public function it_returns_a_403_if_the_route_is_not_owned_by_you(){
        $this->authenticated();
        $route = Route::factory()->create();

        $response = $this->delete(route('route.destroy', $route));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $route = Route::factory()->create();

        $this->delete(route('route.destroy', $route))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_deletes_attached_files(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $files = File::factory()->count(5)->routeMedia()->create();
        File::factory()->count(5)->routeMedia()->create();
        $route->files()->sync($files->pluck('id'));

        $this->assertDatabaseCount('files', 10);

        $response = $this->delete(route('route.destroy', $route));
        $response->assertRedirect(route('route.index'));

        $this->assertDatabaseCount('files', 5);
    }

}
