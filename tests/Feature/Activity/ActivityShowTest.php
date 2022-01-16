<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\File;
use Inertia\Testing\Assert;
use Tests\TestCase;

class ActivityShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_activity(){
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $stat1 = ActivityStats::factory()->create(['activity_id' => $activity->id, 'integration' => 'int1']);
        $stat2 = ActivityStats::factory()->create(['activity_id' => $activity->id, 'integration' => 'int2']);
        $files = File::factory()->activityMedia()->count(5)->create();
        $activity->files()->sync($files);

        $this->get(route('activity.show', $activity))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Show')
                ->has('activity', fn (Assert $page) => $page
                    ->where('id', $activity->id)
                    ->has('files', 5)
                    ->has('stats', 2)
                    ->has('stats.int1', fn(Assert $page) => $page
                        ->where('id', $stat1->id)
                        ->where('integration', 'int1')
                        ->etc()
                    )
                    ->has('stats.int2', fn(Assert $page) => $page
                        ->where('id', $stat2->id)
                        ->where('integration', 'int2')
                        ->etc()
                    )
                    ->etc()
                )
            );
    }

    /** @test */
    public function it_returns_a_403_if_the_activity_is_not_owned_by_you(){
        $this->authenticated();
        $activity = Activity::factory()->create();

        $this->get(route('activity.show', $activity))
            ->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $activity = Activity::factory()->create();
        $this->get(route('activity.show', $activity))
            ->assertRedirect(route('login'));
    }

}
