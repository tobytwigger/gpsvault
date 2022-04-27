<?php

namespace Feature\Route;

use App\Models\Route;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class RouteFileStoreTest extends TestCase
{

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_route()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');
        $route = Route::factory()->create();

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file],
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_route_does_not_exist()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', 55555), [
            'files' => [$file],
        ]);
        $response->assertStatus(404);
    }

    /** @test */
    public function a_new_file_can_be_stored()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file]
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 1);
        $this->assertEquals('filename.gpx', $route->files()->firstOrFail()->filename);
    }

    /** @test */
    public function many_files_can_be_uploaded()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file1 = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');
        $file2 = UploadedFile::fake()->create('filename2.gpx', 58, 'application/gpx+xml');
        $file3 = UploadedFile::fake()->create('filename3.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file1, $file2, $file3]
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 3);
    }

    /** @test */
    public function the_title_and_caption_can_be_set()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file],
            'title' => 'My Title',
            'caption' => 'This is my full caption'
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 1);
        $this->assertDatabaseHas('files', [
            'title' => 'My Title', 'caption' => 'This is my full caption'
        ]);
    }

    /** @test */
    public function the_title_and_caption_apply_to_all_uploaded_files()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file1 = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');
        $file2 = UploadedFile::fake()->create('filename2.gpx', 58, 'application/gpx+xml');
        $file3 = UploadedFile::fake()->create('filename3.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file1, $file2, $file3],
            'title' => 'My Title',
            'caption' => 'This is my full caption'
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 3);
        $this->assertDatabaseHas('files', ['title' => 'My Title', 'caption' => 'This is my full caption', 'filename' => 'filename.gpx']);
        $this->assertDatabaseHas('files', ['title' => 'My Title', 'caption' => 'This is my full caption', 'filename' => 'filename2.gpx']);
        $this->assertDatabaseHas('files', ['title' => 'My Title', 'caption' => 'This is my full caption', 'filename' => 'filename3.gpx']);
    }

    /** @test */
    public function it_directs_you_back_to_view_the_route()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file]
        ]);
        $response->assertRedirect(route('route.show', $route));
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');
        $route = Route::factory()->create();

        $response = $this->post(route('route.file.store', $route), [
            'files' => [$file],
        ]);
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

        Storage::fake('test-fake');
        $fakeFile = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.file.store', $route), array_merge([$key => $value], $key === 'files' ? [] : ['files' => [$fakeFile]]));
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
            ['files', 'not-a-file', 'The files must be an array.'],
            ['files', ['not-a-file'], ['files.0' => 'The files.0 must be a file.']],
            ['title', Str::random(300), 'The title must not be greater than 255 characters.'],
            ['title', null, false],
            ['title', 'This is a valid title', false],
            ['caption', Str::random(65536), 'The caption must not be greater than 65535 characters.'],
            ['caption', null, false],
            ['caption', 'This is a valid caption', false],
        ];
    }
}
