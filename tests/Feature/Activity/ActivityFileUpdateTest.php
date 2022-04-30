<?php

namespace Feature\Activity;

use App\Models\Activity;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActivityFileUpdateTest extends TestCase
{

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_file()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityMedia()->create();
        $activity->files()->sync($file);

        $response = $this->put(route('activity.file.update', [$activity, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_activity()
    {
        $this->authenticated();
        $activity = Activity::factory()->create();
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->files()->sync($file);

        $response = $this->put(route('activity.file.update', [$activity, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_exist()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('activity.file.update', [$activity, 55555]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_activity_does_not_exist()
    {
        $this->authenticated();
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('activity.file.update', [55555, $file]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_belong_to_the_activity_as_a_media_file()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->file_id = $file->id;
        Model::withoutEvents(fn () => $activity->save());

        $response = $this->put(route('activity.file.update', [$activity, $file]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function the_title_and_caption_can_be_updated()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->files()->sync($file);

        $response = $this->put(route('activity.file.update', [$activity, $file]), [
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
    public function it_redirects_back_to_activity_show()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->files()->sync($file);

        $response = $this->put(route('activity.file.update', [$activity, $file]), [
            'title' => 'My Title New',
            'caption' => 'This is my full caption new',
        ]);
        $response->assertRedirect(route('activity.show', $activity));
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activity = Activity::factory()->create();
        $file = File::factory()->activityMedia()->create();
        $activity->files()->sync($file);

        $response = $this->put(route('activity.file.update', [$activity, $file]), []);
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
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->files()->sync($file);

        $response = $this->put(route('activity.file.update', [$activity, $file]), [$key => $value]);
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
