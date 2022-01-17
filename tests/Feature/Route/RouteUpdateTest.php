<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class RouteUpdateTest extends TestCase
{

    /** @test */
    public function a_route_can_be_updated(){
        $this->authenticated();
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');
        Storage::fake('test-fake');

        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description', 'notes' => 'Old Notes', 'file_id' => null]);
        $this->assertNull($route->file_id);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description', 'notes' => 'Updated Notes', 'file' => $file]);

        $this->assertDatabaseHas('routes', [
            'id' => $route->id, 'name' => 'New Name', 'description' => 'New Description', 'notes' => 'Updated Notes'
        ]);

        $this->assertNotNull($route->refresh()->file_id);
    }

    /** @test */
    public function it_redirects_to_show_the_updated_route(){
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertRedirect(route('route.show', $route));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates($key, $value, $error){
        $this->authenticated();
        if(is_callable($value)) {
            $value = call_user_func($value, $this->user);
        }

        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('route.update', $route), [$key => $value]);

        if(!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', true, 'The name must be a string.'],
            ['name', 'This is a new name', false],
            ['description', Str::random(65536), 'The description must not be greater than 65535 characters.'],
            ['description', true, 'The description must be a string.'],
            ['description', 'This is a new description', false],
            ['notes', Str::random(65536), 'The notes must not be greater than 65535 characters.'],
            ['notes', true, 'The notes must be a string.'],
            ['notes', 'This is a new notes', false],
            ['file', fn() => UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml'), false],
            ['file', null, false],
            ['file', 'This is not a file', 'The file must be a file.']
        ];
    }

    /** @test */
    public function you_must_be_authenticated(){
        $route = Route::factory()->create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_update_your_own_route(){
        $this->authenticated();
        $route = Route::factory()->create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertStatus(403);
    }
}