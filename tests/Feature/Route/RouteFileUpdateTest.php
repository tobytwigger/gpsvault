<?php

namespace Feature\Route;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\File;
use App\Models\Route;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Tests\TestCase;

class RouteFileUpdateTest extends TestCase
{
    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_file()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeMedia()->create();
        $route->files()->sync($file);

        $response = $this->put(route('route.file.update', [$route, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_route()
    {
        $this->authenticated();
        $route = Route::factory()->create();
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);
        $route->files()->sync($file);

        $response = $this->put(route('route.file.update', [$route, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_exist()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('route.file.update', [$route, 55555]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_route_does_not_exist()
    {
        $this->authenticated();
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('route.file.update', [55555, $file]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function the_title_and_caption_can_be_updated()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);
        $route->files()->sync($file);

        $response = $this->put(route('route.file.update', [$route, $file]), [
            'title' => 'My Title New',
            'caption' => 'This is my full caption new',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 1);
        $this->assertDatabaseHas('files', [
            'title' => 'My Title New', 'caption' => 'This is my full caption new',
        ]);
    }

    /** @test */
    public function it_redirects_back_to_route_show()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);
        $route->files()->sync($file);

        $response = $this->put(route('route.file.update', [$route, $file]), [
            'title' => 'My Title New',
            'caption' => 'This is my full caption new',
        ]);
        $response->assertRedirect(route('route.show', $route));
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create();
        $file = File::factory()->routeMedia()->create();
        $route->files()->sync($file);

        $response = $this->put(route('route.file.update', [$route, $file]), []);
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     * @param mixed $key
     * @param mixed $value
     * @param mixed $error
     */
    public function it_validates($key, $value, $error)
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeMedia()->create(['user_id' => $this->user->id]);
        $route->files()->sync($file);

        $response = $this->put(route('route.file.update', [$route, $file]), [$key => $value]);
        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            if (is_array($error)) {
                $response->assertSessionHasErrors($error);
            } else {
                $response->assertSessionHasErrors([$key => $error]);
            }
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['title', Str::random(300), 'The title must not be greater than 255 characters.'],
            ['title', null, false],
            ['title', 'This is a valid title', false],
            ['caption', Str::random(65536), 'The caption must not be greater than 65535 characters.'],
            ['caption', null, false],
            ['caption', 'This is a valid caption', false],
        ];
    }
}
