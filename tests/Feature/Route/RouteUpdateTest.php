<?php

namespace Tests\Feature\Route;

use App\Jobs\AnalyseRouteFile;
use App\Models\File;
use App\Models\Route;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Utils\MocksFilepond;

class RouteUpdateTest extends TestCase
{
    use MocksFilepond;

    /** @test */
    public function a_route_can_be_updated()
    {
        $this->authenticated();
        Storage::fake('test-fake');

        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description', 'notes' => 'Old Notes']);
        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description', 'notes' => 'Updated Notes']);

        $this->assertDatabaseHas('routes', [
            'id' => $route->id, 'name' => 'New Name', 'description' => 'New Description', 'notes' => 'Updated Notes',
        ]);
    }

    /** @test */
    public function a_file_can_be_uploaded()
    {
        $this->markTestSkipped('Waiting for rewrite of route file uploads.');
    }

    /** @test */
    public function it_redirects_to_show_the_updated_route()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertRedirect(route('route.show', $route));
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
        if (is_callable($value)) {
            $value = call_user_func($value, $this->user);
        }

        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('route.update', $route), [$key => $value]);
        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
//            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
//            ['name', true, 'The name must be a string.'],
//            ['name', 'This is a new name', false],
//            ['description', Str::random(65536), 'The description must not be greater than 65535 characters.'],
//            ['description', true, 'The description must be a string.'],
//            ['description', 'This is a new description', false],
//            ['notes', Str::random(65536), 'The notes must not be greater than 65535 characters.'],
//            ['notes', true, 'The notes must be a string.'],
//            ['notes', 'This is a new notes', false],
//            ['file', fn () => $this->createFile('filename.gpx', 58, 'application/gpx+xml'), false],
//            ['file', null, false],
            ['file', 'This is not a file', 'The file is not an array'],
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $route = Route::factory()->create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_update_your_own_route()
    {
        $this->authenticated();
        $route = Route::factory()->create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('route.update', $route), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertStatus(403);
    }

    /** @test */
    public function it_fires_an_analysis_job_if_a_file_is_given()
    {
        Bus::fake(AnalyseRouteFile::class);
        $this->authenticated();
        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $response = $this->put(route('route.update', $route), [
            'file' => $file->toArray(),
        ]);

        Bus::assertDispatched(AnalyseRouteFile::class, fn (AnalyseRouteFile $job) => $job->route->file->filename === 'filename.gpx');
    }

    /** @test */
    public function it_does_not_fire_an_analysis_job_if_a_file_is_not_given()
    {
        Bus::fake(AnalyseRouteFile::class);
        $this->authenticated();

        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $response = $this->put(route('route.update', $route));

        Bus::assertNotDispatched(AnalyseRouteFile::class);
    }

    /** @test */
    public function it_fires_an_analysis_job_even_when_a_route_file_already_exists()
    {
        Bus::fake(AnalyseRouteFile::class);
        $this->authenticated();
        Storage::fake('test-fake');
        $oldFile = File::factory()->activityFile()->create(['filename' => 'old.gpx']);
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $route = Route::factory()->create(['user_id' => $this->user->id, 'file_id' => $oldFile->id]);
        $response = $this->put(route('route.update', $route), [
            'file' => $file->toArray(),
        ]);

        Bus::assertDispatched(AnalyseRouteFile::class, fn (AnalyseRouteFile $job) => $job->route->file->filename === 'filename.gpx');
    }
}
