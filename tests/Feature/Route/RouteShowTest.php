<?php

namespace Tests\Feature\Route;

use App\Models\File;
use App\Models\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RouteShowTest extends TestCase
{
    /** @test */
    public function it_shows_the_route()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $files = File::factory()->routeMedia()->count(5)->create();
        $route->files()->sync($files);

        $this->get(route('route.show', $route))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Route/Show')
                    ->has(
                        'routeModel',
                        fn (Assert $page) => $page
                            ->where('id', $route->id)
                            ->has('files', 5)
                            ->etc()
                    )
            );
    }

    /** @test */
    public function it_returns_a_403_if_the_route_is_not_owned_by_you()
    {
        $this->authenticated();
        $route = Route::factory()->create(['public' => true]);

        $this->get(route('route.show', $route))
            ->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create();
        $this->get(route('route.show', $route))
            ->assertRedirect(route('login'));
    }
}
