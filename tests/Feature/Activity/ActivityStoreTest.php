<?php

namespace Tests\Feature\Activity;

use App\Jobs\AnalyseActivityFile;
use App\Models\Activity;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActivityStoreTest extends TestCase
{

    /** @test */
    public function it_creates_an_activity_from_a_file()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.store'), [
            'file' => $file,
        ]);

        $this->assertDatabaseCount('activities', 1);

        $this->assertDatabaseHas('files', [
            'filename' => 'filename.gpx',
            'disk' => 'test-fake',
        ]);
        $file = File::where('filename', 'filename.gpx')->firstOrFail();
        $this->assertDatabaseHas('activities', [
            'file_id' => $file->id,
        ]);
    }

    /** @test */
    public function it_fires_an_analysis_job()
    {
        Bus::fake(AnalyseActivityFile::class);
        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.store'), [
            'file' => $file,
        ]);

        Bus::assertDispatched(AnalyseActivityFile::class, fn (AnalyseActivityFile $job) => $job->model instanceof Activity && $job->model->file->filename === 'filename.gpx');
    }

    /** @test */
    public function it_redirects_to_show_the_new_activity()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.store'), [
            'file' => $file,
        ]);

        $this->assertDatabaseCount('activities', 1);
        $response->assertRedirect(route('activity.show', Activity::firstOrFail()));
    }

    /** @test */
    public function a_name_can_be_set()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.store'), [
            'file' => $file,
            'name' => 'This is the activity name',
        ]);

        $this->assertDatabaseCount('activities', 1);
        $this->assertDatabaseHas('activities', [
            'name' => 'This is the activity name',
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

        Storage::fake('test-fake');
        $fakeFile = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.store'), array_merge([$key => $value], $key === 'file' ? [] : ['file' => $fakeFile]));
        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', null, false],
            ['name', 'This is a valid namne', false],
            ['file', null, 'The file field is required.'],
            ['file', 'This is not a file', 'The file must be a file.'],
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        Storage::fake('test-fake');
        $file = UploadedFile::fake()->create('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.store'), [
            'file' => $file,
            'name' => 'This is the activity name',
        ]);
        $response->assertRedirect(route('login'));
    }
}
