<?php

namespace Feature\Route;

use App\Models\Route;
use Carbon\Carbon;
use Tests\TestCase;

class RouteSearchTest extends TestCase
{

    /** @test */
    public function it_returns_all_routes_sorted_by_updated_at_when_no_query_given()
    {
        $this->authenticated();

        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id]);
        $routes[0]->updated_at = Carbon::now()->subDays(2);
        $routes[1]->updated_at = Carbon::now()->subDays(3);
        $routes[2]->updated_at = Carbon::now()->subDays(6);
        $routes[3]->updated_at = Carbon::now()->subDays(4);
        $routes[4]->updated_at = Carbon::now()->subDays(1);
        $routes->map->save();

        $response = $this->getJson(route('route.search', ['query' => null]));
        $response->assertOk();
        $response->assertJsonCount(5);
        $json = $response->decodeResponseJson();

        $this->assertEquals($routes[4]->id, $json[0]['id']);
        $this->assertEquals($routes[0]->id, $json[1]['id']);
        $this->assertEquals($routes[1]->id, $json[2]['id']);
        $this->assertEquals($routes[3]->id, $json[3]['id']);
        $this->assertEquals($routes[2]->id, $json[4]['id']);
    }

    /** @test */
    public function it_limits_to_15_routes()
    {
        $this->authenticated();

        $routes = Route::factory()->count(20)->create(['user_id' => $this->user->id]);
        foreach ($routes as $index => $route) {
            $route->updated_at = Carbon::now()->subDays($index);
            $route->save();
        }

        $response = $this->getJson(route('route.search', ['query' => null]));
        $response->assertJsonCount(15);
        $json = $response->decodeResponseJson();

        foreach ($routes->take(15) as $index => $route) {
            $this->assertEquals($route->id, $json[$index]['id']);
        }
    }

    /** @test */
    public function it_only_returns_your_routes()
    {
        $this->authenticated();

        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id]);
        Route::factory()->count(50)->create();
        $response = $this->getJson(route('route.search', ['query' => null]));
        $response->assertJsonCount(5);
        $response->assertJsonFragment(['id' => $routes[0]->id]);
        $response->assertJsonFragment(['id' => $routes[1]->id]);
        $response->assertJsonFragment(['id' => $routes[2]->id]);
        $response->assertJsonFragment(['id' => $routes[3]->id]);
        $response->assertJsonFragment(['id' => $routes[4]->id]);
    }

    /** @test */
    public function it_filters_by_name()
    {
        $this->authenticated();

        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different name']);

        $response = $this->getJson(route('route.search', ['query' => 'name']));
        $response->assertJsonCount(6);

        $response = $this->getJson(route('route.search', ['query' => 'different']));
        $response->assertJsonCount(1);
    }

    /** @test */
    public function filtering_is_not_case_sensitive()
    {
        $this->authenticated();

        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different Name']);

        $response = $this->getJson(route('route.search', ['query' => 'name']));
        $response->assertJsonCount(6);
    }
}
