<?php

namespace Tests\Feature\Api\Route;

use App\Models\Route;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class RouteShowText extends TestCase
{
    /** @test */
    public function show_returns_the_route()
    {
        $this->authenticatedWithSanctum();
        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('api.route.show', $route->id));
        $response->assertOk();
        $response->assertJson($route->toArray());
    }

    /** @test */
    public function it_throws_an_exception_if_you_do_not_own_the_route(){
        $this->authenticatedWithSanctum();
        $route = Route::factory()->create(['user_id' => User::factory()->create()->id]);

        $response = $this->getJson(route('api.route.show', $route->id));
        $response->assertForbidden();
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.route.index'))->assertUnauthorized();
    }
}
