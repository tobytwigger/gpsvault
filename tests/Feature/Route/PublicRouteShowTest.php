<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use App\Models\Stats;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicRouteShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_route()
    {
        $route = Route::factory()->create(['public' => true]);
        $stat1 = Stats::factory()->route($route)->create(['integration' => 'int1']);
        $stat2 = Stats::factory()->route($route)->create(['integration' => 'int2']);

        $this->get(route('route.public', $route))
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Route/Public')
                    ->has(
                        'routeModel',
                        fn (Assert $page) => $page
                    ->where('id', $route->id)
                    ->has('stats', 2)
                    ->has(
                        'stats.0',
                        fn (Assert $page) => $page
                    ->where('id', $stat1->id)
                    ->where('integration', 'int1')
                    ->etc()
                    )
                    ->has(
                        'stats.1',
                        fn (Assert $page) => $page
                    ->where('id', $stat2->id)
                    ->where('integration', 'int2')
                    ->etc()
                    )
                    ->etc()
                    )
            );
    }

    /** @test */
    public function it_returns_a_403_if_the_route_is_not_public()
    {
        $route = Route::factory()->create(['public' => false]);

        $this->get(route('route.public', $route))
            ->assertStatus(403);
    }

    /** @test */
    public function you_can_be_authenticated()
    {
        $this->authenticated();

        $route = Route::factory()->create(['public' => true]);
        $this->get(route('route.public', $route))
            ->assertInertia(fn (Assert $page) => $page->component('Route/Public'));
    }
}
