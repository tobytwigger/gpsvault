<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Place;
use App\Models\Route;
use App\Models\User;
use Tests\TestCase;

class RouteTest extends TestCase
{

    /** @test */
    public function the_user_id_is_automatically_set_on_creation_if_null()
    {
        $this->authenticated();
        $route = Route::factory()->make(['user_id' => null]);
        $route->save();

        $this->assertEquals($this->user->id, $route->refresh()->user_id);
    }

    /** @test */
    public function cover_image_returns_the_preview_for_the_file()
    {
        $route = Route::factory()->create();
        $file = File::factory()->routeMedia()->create();
        $route->files()->attach($file);

        $this->assertEquals(route('file.preview', $file), $route->cover_image);
    }

    /** @test */
    public function cover_image_returns_null_if_file_not_set()
    {
        $route = Route::factory()->create();
        $this->assertNull($route->cover_image);
    }

    /** @test */
    public function it_has_a_relationship_with_user()
    {
        $user = User::factory()->create();
        $route = Route::factory()->create(['user_id' => $user]);

        $this->assertInstanceOf(User::class, $route->user);
        $this->assertTrue($user->is($route->user));
    }

    /** @test */
    public function it_has_a_relationship_with_files()
    {
        $route = Route::factory()->create();
        $files = File::factory()->routeMedia()->count(5)->create();
        $route->files()->attach($files);

        $this->assertContainsOnlyInstancesOf(File::class, $route->files);
        foreach ($route->files as $file) {
            $this->assertTrue($files->shift()->is($file));
        }
    }

    /** @test */
    public function it_has_many_places()
    {
        $route = Route::factory()->create();

        $place1 = Place::factory()->create();
        $place2 = Place::factory()->create();
        $place3 = Place::factory()->create();

        $route->places()->attach([$place1->id, $place2->id]);

        $this->assertCount(2, $route->places);
        $this->assertTrue($place1->is($route->places[0]));
        $this->assertTrue($place2->is($route->places[1]));
    }

    /** @test */
    public function it_has_a_relationship_with_paths()
    {
    }
}
