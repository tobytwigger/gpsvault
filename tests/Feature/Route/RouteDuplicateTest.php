<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use App\Models\File;
use Tests\TestCase;

class RouteDuplicateTest extends TestCase
{

    /** @test */
    public function it_identifies_if_a_file_is_not_a_duplicate(){
        $this->authenticated();

        $response = $this->postJson(route('route.file.duplicate'), ['hash' => '123']);

        $response->assertJsonFragment(['is_duplicate' => false]);
    }

    /** @test */
    public function it_identifies_if_the_file_is_a_duplicate(){
        $this->authenticated();

        $file = File::factory()->routeFile()->create(['user_id' => $this->user->id, 'hash' => 'duplicatedhash']);
        $route = Route::factory()->create(['user_id' => $this->user->id, 'route_file_id' => $file->id]);
        $response = $this->postJson(route('route.file.duplicate'), ['hash' => 'duplicatedhash']);

        $response->assertJsonFragment(['is_duplicate' => true]);
    }

    /** @test */
    public function it_returns_which_file_and_route_is_the_duplicate(){
        $this->authenticated();

        $file = File::factory()->routeFile()->create(['user_id' => $this->user->id, 'hash' => 'duplicatedhash']);
        $route = Route::factory()->create(['user_id' => $this->user->id, 'route_file_id' => $file->id]);
        $response = $this->postJson(route('route.file.duplicate'), ['hash' => 'duplicatedhash']);

        $response->assertJsonFragment(['is_duplicate' => true]);
        $json = $response->decodeResponseJson();
        $this->assertEquals($json['file']['id'], $file->id);
        $this->assertEquals($json['route']['id'], $route->id);
    }

    /** @test */
    public function it_only_checks_your_routes(){
        $this->authenticated();

        $file = File::factory()->routeFile()->create(['user_id' => $this->user->id, 'hash' => 'duplicatedhash']);
        $route = Route::factory()->create(['route_file_id' => $file->id]);
        $response = $this->postJson(route('route.file.duplicate'), ['hash' => 'duplicatedhash']);

        $response->assertJsonFragment(['is_duplicate' => false]);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $response = $this->postJson(route('route.file.duplicate'), ['hash' => 'duplicatedhash'])
            ->assertStatus(401);
    }

}
