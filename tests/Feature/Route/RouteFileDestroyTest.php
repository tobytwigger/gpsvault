<?php

namespace Tests\Feature\Route;

use App\Models\File;
use App\Models\Route;
use Tests\TestCase;

class RouteFileDestroyTest extends TestCase
{
    /** @test */
    public function it_deletes_the_route_file()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);
        $route->files()->sync($file);

        $response = $this->delete(route('route.file.destroy', [$route, $file]));
        $response->assertRedirect(route('route.show', $route));

        $this->assertDatabaseCount('files', 0);
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_file()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeMedia()->create();
        $route->files()->sync($file);

        $response = $this->delete(route('route.file.destroy', [$route, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_route()
    {
        $this->authenticated();
        $route = Route::factory()->create();
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);
        $route->files()->sync($file);

        $response = $this->delete(route('route.file.destroy', [$route, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_exist()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('route.file.destroy', [$route, 55555]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_route_does_not_exist()
    {
        $this->authenticated();
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('route.file.destroy', [55555, $file]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create();
        $file = File::factory()->routeMedia()->create();
        $route->files()->sync($file);

        $this->delete(route('route.file.destroy', [$route, $file]))
            ->assertRedirect(route('login'));
    }
}
