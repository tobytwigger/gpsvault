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

class RouteStoreTest extends TestCase
{
    /** @test */
    public function it_creates_an_route_from_a_file()
    {
        Bus::fake([AnalyseRouteFile::class]);

        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.store'), [
            'file' => $file, 'name' => 'Test',
        ]);

        $this->assertDatabaseCount('routes', 1);

        $this->assertDatabaseHas('files', [
            'filename' => 'filename.gpx',
            'disk' => 'test-fake',
        ]);
        $file = File::where('filename', 'filename.gpx')->firstOrFail();
        $this->assertDatabaseHas('routes', [
            'file_id' => $file->id,
        ]);
    }

    /** @test */
    public function it_redirects_to_show_the_new_route()
    {
        Bus::fake([AnalyseRouteFile::class]);

        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.store'), [
            'file' => $file, 'name' => 'Test',
        ]);

        $this->assertDatabaseCount('routes', 1);
        $response->assertRedirect(route('route.show', Route::firstOrFail()));
    }

    /** @test */
    public function a_name_and_description_can_be_set()
    {
        $this->authenticated();

        $response = $this->post(route('route.store'), [
            'name' => 'This is the route name',
            'description' => 'A route description',
        ]);

        $this->assertDatabaseCount('routes', 1);
        $this->assertDatabaseHas('routes', [
            'name' => 'This is the route name',
            'description' => 'A route description',
        ]);
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

        Storage::fake('test-fake');

        $response = $this->post(route('route.store'), [$key => $value]);
        if (!$error) {
            $response->assertSessionMissing($key);
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', null, 'The name field is required.'],
            ['name', 'This is a valid namne', false],
            ['description', Str::random(65570), 'The description must not be greater than 65535 characters.'],
            ['description', null, false],
            ['description', 'This is a valid namne', false],
            ['file', fn () => UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml'), false],
            ['file', null, false],
            ['file', 'This is not a file', 'The file must be a file.'],
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.store'), [
            'file' => $file,
            'name' => 'This is the route name',
        ]);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_dispatches_the_analysis_job()
    {
        Bus::fake();

        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('route.store'), [
            'file' => $file, 'name' => 'Test',
        ]);

        Bus::assertDispatched(AnalyseRouteFile::class, fn (AnalyseRouteFile $job) => $job->route->file->filename === 'filename.gpx');
    }

    /** @test */
    public function it_does_not_fire_an_analysis_job_if_a_file_is_not_given()
    {
        Bus::fake(AnalyseRouteFile::class);
        $this->authenticated();

        $response = $this->post(route('route.store'), ['name' => 'Test']);

        Bus::assertNotDispatched(AnalyseRouteFile::class);
    }

    /** @test */
    public function it_does_not_fire_an_analysis_job_if_a_name_is_not_given()
    {
        Bus::fake(AnalyseRouteFile::class);
        $this->authenticated();
        Storage::fake('test-fake');

        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');
        $response = $this->post(route('route.store'), ['file' => $file]);

        Bus::assertNotDispatched(AnalyseRouteFile::class);
    }
}
