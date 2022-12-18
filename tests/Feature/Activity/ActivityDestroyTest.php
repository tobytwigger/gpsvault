<?php

namespace Tests\Feature\Activity;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\Activity;
use App\Models\File;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ActivityDestroyTest extends TestCase
{
    /** @test */
    public function it_deletes_the_activity()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $this->assertDatabaseHas('activities', ['id' => $activity->id]);

        $response = $this->delete(route('activity.destroy', $activity));
        $response->assertRedirect(route('activity.index'));

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    /** @test */
    public function it_returns_a_403_if_the_activity_is_not_owned_by_you()
    {
        $this->authenticated();
        $activity = Activity::factory()->create();

        $response = $this->delete(route('activity.destroy', $activity));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activity = Activity::factory()->create();

        $this->delete(route('activity.destroy', $activity))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_deletes_attached_files()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $files = File::factory()->count(5)->activityMedia()->create();
        File::factory()->count(5)->activityMedia()->create();

        $activity->files()->sync($files->pluck('id'));

        $this->assertDatabaseCount('files', 10);

        $response = $this->delete(route('activity.destroy', $activity));
        $response->assertRedirect(route('activity.index'));

        $this->assertDatabaseCount('files', 5);
    }
}
