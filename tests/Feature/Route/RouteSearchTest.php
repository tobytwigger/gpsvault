<?php

namespace Feature\Route;

use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Testing\AssertableJsonString;
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

        $response = $this->getJson(route('route.search', ['query' => null, 'perPage' => 100]));
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $json = $response->decodeResponseJson()['data'];

        $this->assertEquals($routes[4]->id, $json[0]['id']);
        $this->assertEquals($routes[0]->id, $json[1]['id']);
        $this->assertEquals($routes[1]->id, $json[2]['id']);
        $this->assertEquals($routes[3]->id, $json[3]['id']);
        $this->assertEquals($routes[2]->id, $json[4]['id']);
    }

    /** @test */
    public function it_paginates_the_responses()
    {
        $this->authenticated();

        $routes = Route::factory()->count(50)->create(['user_id' => $this->user->id]);
        $routes[0]->updated_at = Carbon::now()->addDays(10);
        $routes[1]->updated_at = Carbon::now()->addDays(9);
        $routes[2]->updated_at = Carbon::now()->addDays(8);
        $routes[3]->updated_at = Carbon::now()->addDays(7);
        $routes[4]->updated_at = Carbon::now()->addDays(6);
        $routes[5]->updated_at = Carbon::now()->addDays(5);
        $routes[6]->updated_at = Carbon::now()->addDays(4);
        $routes[7]->updated_at = Carbon::now()->addDays(3);
        $routes[8]->updated_at = Carbon::now()->addDays(2);
        $routes[9]->updated_at = Carbon::now()->addDays(1);
        $routes->map->save();

        $response = $this->getJson(route('route.search', ['query' => null, 'perPage' => 5, 'page' => 1]));
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $json = $response->decodeResponseJson()['data'];

        $this->assertEquals($routes[0]->id, $json[0]['id']);
        $this->assertEquals($routes[1]->id, $json[1]['id']);
        $this->assertEquals($routes[2]->id, $json[2]['id']);
        $this->assertEquals($routes[3]->id, $json[3]['id']);
        $this->assertEquals($routes[4]->id, $json[4]['id']);

        $response = $this->getJson(route('route.search', ['query' => null, 'perPage' => 5, 'page' => 2]));
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $json = $response->decodeResponseJson()['data'];

        $this->assertEquals($routes[5]->id, $json[0]['id']);
        $this->assertEquals($routes[6]->id, $json[1]['id']);
        $this->assertEquals($routes[7]->id, $json[2]['id']);
        $this->assertEquals($routes[8]->id, $json[3]['id']);
        $this->assertEquals($routes[9]->id, $json[4]['id']);


    }

    /** @test */
    public function it_limits_to_a_set_number_of_routes()
    {
        $this->authenticated();

        $routes = Route::factory()->count(20)->create(['user_id' => $this->user->id]);
        foreach ($routes as $index => $route) {
            $route->updated_at = Carbon::now()->subDays($index);
            $route->save();
        }

        $response = $this->getJson(route('route.search', ['query' => null, 'perPage' => 15]));
        $response->assertJsonCount(15, 'data');
        $json = $response->decodeResponseJson()['data'];

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
        $response = $this->getJson(route('route.search', ['query' => null, 'perPage' => 100]));
        $response->assertJsonCount(5, 'data');
        $json = new AssertableJsonString($response->json('data'));

        $json->assertFragment(['id' => $routes[0]->id]);
        $json->assertFragment(['id' => $routes[1]->id]);
        $json->assertFragment(['id' => $routes[2]->id]);
        $json->assertFragment(['id' => $routes[3]->id]);
        $json->assertFragment(['id' => $routes[4]->id]);
    }

    /** @test */
    public function it_filters_by_name()
    {
        $this->authenticated();

        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different name']);

        $response = $this->getJson(route('route.search', ['query' => 'name', 'perPage' => 100]));
        $response->assertJsonCount(6, 'data');

        $response = $this->getJson(route('route.search', ['query' => 'different', 'perPage' => 100]));
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function filtering_is_not_case_sensitive()
    {
        $this->authenticated();

        $routes = Route::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different Name']);

        $response = $this->getJson(route('route.search', ['query' => 'name', 'perPage' => 100]));
        $response->assertJsonCount(6, 'data');
    }
}
