<?php

namespace Tests\Feature\Api\Route;

use App\Models\Route;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RouteIndexTest extends TestCase
{
    /** @test */
    public function index_loads_routes_ordered_by_date()
    {
        $this->authenticatedWithSanctum();
        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id]);
        $routes[0]->updated_at = Carbon::now()->subDays(4);
        $routes[1]->updated_at = Carbon::now()->subDays(2);
        $routes[2]->updated_at = Carbon::now()->subDays(1);
        $routes[3]->updated_at = Carbon::now()->subDays(11);
        $routes[4]->updated_at = Carbon::now()->subDays(4);
        $routes->map->save();

        $this->getJson(route('api.route.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $routes[2]->id)
            ->assertJsonPath('data.1.id', $routes[1]->id)
            ->assertJsonPath('data.2.id', $routes[0]->id)
            ->assertJsonPath('data.3.id', $routes[4]->id)
            ->assertJsonPath('data.4.id', $routes[3]->id);
    }

    /** @test */
    public function index_paginates_routes()
    {
        $this->authenticatedWithSanctum();
        $routes = Route::factory()->count(20)->create(['user_id' => $this->user->id, 'updated_at' => null]);
        foreach ($routes as $index => $route) {
            $route->updated_at = Carbon::now()->subDays($index);
            $route->save();
        }

        $this->getJson(route('api.route.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $routes[4]->id)
            ->assertJsonPath('data.1.id', $routes[5]->id)
            ->assertJsonPath('data.2.id', $routes[6]->id)
            ->assertJsonPath('data.3.id', $routes[7]->id);
    }

    /** @test */
    public function index_only_returns_your_routes()
    {
        $this->authenticatedWithSanctum();
        $routes = Route::factory()->count(3)->create(['user_id' => $this->user->id, 'updated_at' => null]);
        Route::factory()->count(2)->create(['updated_at' => null]);

        $this->getJson(route('api.route.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $routes[0]->id)
            ->assertJsonPath('data.1.id', $routes[1]->id)
            ->assertJsonPath('data.2.id', $routes[2]->id);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.route.index'))->assertUnauthorized();
    }
}
