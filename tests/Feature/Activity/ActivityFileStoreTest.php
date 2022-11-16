<?php

namespace Feature\Activity;

use App\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Utils\MocksFilepond;

class ActivityFileStoreTest extends TestCase
{
    use MocksFilepond;

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_activity()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');
        $activity = Activity::factory()->create();

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file->toArray()],
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_activity_does_not_exist()
    {
        $this->authenticated();
        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', 55555), [
            'files' => [$file->toArray()],
        ]);
        $response->assertStatus(404);
    }

    /** @test */
    public function a_new_file_can_be_stored()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');

        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file->toArray()],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 1);
        $this->assertEquals('filename.gpx', $activity->files()->firstOrFail()->filename);
    }

    /** @test */
    public function many_files_can_be_uploaded()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file1 = $this->createFile('filename.gpx', 58, 'application/gpx+xml');
        $file2 = $this->createFile('filename2.gpx', 58, 'application/gpx+xml');
        $file3 = $this->createFile('filename3.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file1->toArray(), $file2->toArray(), $file3->toArray()],
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 3);
    }

    /** @test */
    public function the_title_and_caption_can_be_set()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file->toArray()],
            'title' => 'My Title',
            'caption' => 'This is my full caption',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 1);
        $this->assertDatabaseHas('files', [
            'title' => 'My Title', 'caption' => 'This is my full caption',
        ]);
    }

    /** @test */
    public function the_title_and_caption_apply_to_all_uploaded_files()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file1 = $this->createFile('filename.gpx', 58, 'application/gpx+xml');
        $file2 = $this->createFile('filename2.gpx', 58, 'application/gpx+xml');
        $file3 = $this->createFile('filename3.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file1->toArray(), $file2->toArray(), $file3->toArray()],
            'title' => 'My Title',
            'caption' => 'This is my full caption',
        ]);
        $response->assertRedirect();

        $this->assertDatabaseCount('files', 3);
        $this->assertDatabaseHas('files', ['title' => 'My Title', 'caption' => 'This is my full caption', 'filename' => 'filename.gpx']);
        $this->assertDatabaseHas('files', ['title' => 'My Title', 'caption' => 'This is my full caption', 'filename' => 'filename2.gpx']);
        $this->assertDatabaseHas('files', ['title' => 'My Title', 'caption' => 'This is my full caption', 'filename' => 'filename3.gpx']);
    }

    /** @test */
    public function it_directs_you_back_to_view_the_activity()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file->toArray()],
        ]);
        $response->assertRedirect(route('activity.show', $activity));
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');
        $activity = Activity::factory()->create();

        $response = $this->post(route('activity.file.store', $activity), [
            'files' => [$file->toArray()],
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
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        Storage::fake('test-fake');
        $file = $this->createFile('filename.gpx', 58, 'application/gpx+xml');

        $response = $this->post(route('activity.file.store', $activity), array_merge([$key => $value], $key === 'files' ? [] : ['files' => [$file->toArray()]]));

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
            ['files', ['not-a-file'], ['files.0' => 'The files.0 is not an array']],
            ['title', Str::random(300), 'The title must not be greater than 255 characters.'],
            ['title', null, false],
            ['title', 'This is a valid title', false],
            ['caption', Str::random(65536), 'The caption must not be greater than 65535 characters.'],
            ['caption', null, false],
            ['caption', 'This is a valid caption', false],
        ];
    }
}
