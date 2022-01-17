<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\Assert;
use Tests\TestCase;

class RouteIndexTest extends TestCase
{

    /** @test */
    public function index_loads_routes_ordered_by_date(){
        $this->authenticated();
        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id]);
        $routes[0]->updated_at = Carbon::now()->subDays(4);
        $routes[1]->updated_at = Carbon::now()->subDays(2);
        $routes[2]->updated_at = Carbon::now()->subDays(1);
        $routes[3]->updated_at = Carbon::now()->subDays(11);
        $routes[4]->updated_at = Carbon::now()->subDays(4);
        $routes->map->save();

        $this->get(route('route.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Route/Index')
                ->has('routes', fn (Assert $page) => $page
                    ->has('data', 5)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $routes[2]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $routes[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $routes[0]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $routes[4]->id)->etc())
                    ->has('data.4', fn(Assert $page) => $page->where('id', $routes[3]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_paginates_routes(){
        $this->authenticated();
        $routes = Route::factory()->count(20)->create(['user_id' => $this->user->id, 'updated_at' => null]);

        $this->get(route('route.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Route/Index')
                ->has('routes', fn (Assert $page) => $page
                    ->has('data', 4)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $routes[4]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $routes[5]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $routes[6]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $routes[7]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_only_returns_your_routes(){
        $this->authenticated();
        $routes = Route::factory()->count(3)->create(['user_id' => $this->user->id, 'updated_at' => null]);
        Route::factory()->count(2)->create(['updated_at' => null]);

        $this->get(route('route.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Route/Index')
                ->has('routes', fn (Assert $page) => $page
                    ->has('data', 3)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $routes[0]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $routes[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $routes[2]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function you_must_be_authenticated(){
        $this->get(route('route.index'))->assertRedirect(route('login'));
    }

}
