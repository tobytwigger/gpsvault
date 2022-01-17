<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use App\Models\RouteStats;
use App\Models\File;
use Inertia\Testing\Assert;
use Tests\TestCase;

class RouteShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_route(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $stat1 = RouteStats::factory()->create(['route_id' => $route->id, 'integration' => 'int1']);
        $stat2 = RouteStats::factory()->create(['route_id' => $route->id, 'integration' => 'int2']);
        $files = File::factory()->routeMedia()->count(5)->create();
        $route->files()->sync($files);

        $this->get(route('route.show', $route))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Route/Show')
                ->has('routeModel', fn (Assert $page) => $page
                    ->where('id', $route->id)
                    ->has('files', 5)
                    ->has('stats', 2)
                    ->has('stats.int1', fn(Assert $page) => $page
                        ->where('id', $stat1->id)
                        ->where('integration', 'int1')
                        ->etc()
                    )
                    ->has('stats.int2', fn(Assert $page) => $page
                        ->where('id', $stat2->id)
                        ->where('integration', 'int2')
                        ->etc()
                    )
                    ->etc()
                )
            );
    }

    /** @test */
    public function it_returns_a_403_if_the_route_is_not_owned_by_you(){
        $this->authenticated();
        $route = Route::factory()->create();

        $this->get(route('route.show', $route))
            ->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $route = Route::factory()->create();
        $this->get(route('route.show', $route))
            ->assertRedirect(route('login'));
    }

}
