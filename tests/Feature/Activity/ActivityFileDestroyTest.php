<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class ActivityFileDestroyTest extends TestCase
{

    /** @test */
    public function it_deletes_the_activity_file()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->files()->sync($file);

        $response = $this->delete(route('activity.file.destroy', [$activity, $file]));
        $response->assertRedirect(route('activity.show', $activity));

        $this->assertDatabaseCount('files', 0);
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_file()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityMedia()->create();
        $activity->files()->sync($file);

        $response = $this->delete(route('activity.file.destroy', [$activity, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_activity()
    {
        $this->authenticated();
        $activity = Activity::factory()->create();
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);
        $activity->files()->sync($file);

        $response = $this->delete(route('activity.file.destroy', [$activity, $file]), []);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_file_does_not_exist()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('activity.file.destroy', [$activity, 55555]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_activity_does_not_exist()
    {
        $this->authenticated();
        $file = File::factory()->activityMedia()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('activity.file.destroy', [55555, $file]), []);
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

        $response = $this->delete(route('activity.file.destroy', [$activity, $file]), []);
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activity = Activity::factory()->create();
        $file = File::factory()->activityMedia()->create();
        $activity->files()->sync($file);

        $this->delete(route('activity.file.destroy', [$activity, $file]))
            ->assertRedirect(route('login'));
    }
}
