<?php

namespace Tests\Unit\Models;

use App\Models\Place;
use App\Models\Route;
use Tests\TestCase;

class PlaceTest extends TestCase
{

    /** @test */
    public function it_has_many_routes()
    {
        /** @var Place $place */
        $place = Place::factory()->create();

        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();
        $route3 = Route::factory()->create();

        $place->routes()->attach([$route1->id, $route2->id]);

        $this->assertCount(2, $place->routes);
        $this->assertTrue($route1->is($place->routes[0]));
        $this->assertTrue($route2->is($place->routes[1]));
    }
}
