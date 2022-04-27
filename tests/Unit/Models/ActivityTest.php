<?php

namespace Tests\Unit\Models;

use App\Integrations\Strava\Models\StravaComment;
use App\Integrations\Strava\Models\StravaKudos;
use App\Models\Activity;
use App\Models\File;
use App\Models\Stats;
use App\Models\User;
use App\Settings\StatsOrder;
use Carbon\Carbon;
use Tests\TestCase;

class ActivityTest extends TestCase
{

    /** @test */
    public function the_user_id_is_automatically_set_on_creation_if_null()
    {
        $this->authenticated();
        $activity = Activity::factory()->make(['user_id' => null]);
        $activity->save();

        $this->assertEquals($this->user->id, $activity->refresh()->user_id);
    }

    /** @test */
    public function it_deletes_related_models()
    {
        $file = File::factory()->activityFile()->create();
        $activity = Activity::factory()->create(['file_id' => $file->id]);
        Stats::factory()->activity($activity)->count(5)->create();
        StravaComment::factory()->count(5)->create(['activity_id' => $activity->id]);
        StravaKudos::factory()->count(5)->create(['activity_id' => $activity->id]);
        $files = File::factory()->activityMedia()->count(5)->create();
        $activity->files()->attach($files);

        $this->assertDatabaseCount('stats', 5);
        $this->assertDatabaseCount('strava_comments', 5);
        $this->assertDatabaseCount('strava_kudos', 5);
        $this->assertDatabaseCount('files', 6);

        $activity->delete();

        $this->assertDatabaseCount('stats', 0);
        $this->assertDatabaseCount('strava_comments', 0);
        $this->assertDatabaseCount('strava_kudos', 0);
        $this->assertDatabaseCount('files', 0);
    }

    /** @test */
    public function cover_image_returns_the_preview_for_the_file()
    {
        $activity = Activity::factory()->create();
        $file = File::factory()->activityMedia()->create();
        $activity->files()->attach($file);

        $this->assertEquals(route('file.preview', $file), $activity->cover_image);
    }

    /** @test */
    public function cover_image_returns_null_if_file_not_set()
    {
        $activity = Activity::factory()->create();
        $this->assertNull($activity->cover_image);
    }

    /** @test */
    public function it_has_a_relationship_with_strava_comments()
    {
        $activity = Activity::factory()->create();
        $stravaComments = StravaComment::factory()->count(5)->create(['activity_id' => $activity->id]);

        $this->assertContainsOnlyInstancesOf(StravaComment::class, $activity->stravaComments);
        $this->assertEquals($stravaComments->toArray(), $activity->stravaComments->toArray());
    }

    /** @test */
    public function it_has_a_relationship_with_strava_kudos()
    {
        $activity = Activity::factory()->create();
        $stravaKudos = StravaKudos::factory()->count(5)->create(['activity_id' => $activity->id]);

        $this->assertContainsOnlyInstancesOf(StravaKudos::class, $activity->stravaKudos);
        $this->assertEquals($stravaKudos->toArray(), $activity->stravaKudos->toArray());
    }

    /** @test */
    public function it_has_a_relationship_with_user()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $user]);

        $this->assertInstanceOf(User::class, $activity->user);
        $this->assertTrue($user->is($activity->user));
    }

    /** @test */
    public function it_has_a_relationship_with_files()
    {
        $activity = Activity::factory()->create();
        $files = File::factory()->activityMedia()->count(5)->create();
        $activity->files()->attach($files);

        $this->assertContainsOnlyInstancesOf(File::class, $activity->files);
        foreach ($activity->files as $file) {
            $this->assertTrue($files->shift()->is($file));
        }
    }

    /** @test */
    public function it_can_be_linked_to_an_integration_and_queried_as_such()
    {
        $activity = Activity::factory()->create();
        $activity2 = Activity::factory()->create();
        $activity->linkTo('strava');
        $this->assertCount(1, Activity::linkedTo('strava')->get());
    }

    /** @test */
    public function order_by_started_at_orders_by_started_at()
    {
        $activity1 = Activity::factory()->create();
        Stats::factory()->activity($activity1)->create(['started_at' => Carbon::now()]);

        $activity2 = Activity::factory()->create();
        Stats::factory()->activity($activity2)->create(['started_at' => Carbon::now()->subDays(2)]);

        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['started_at' => Carbon::now()->subDays(1)]);

        $activities = Activity::orderByStat('started_at')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity3->is($activities[1]));
        $this->assertTrue($activity2->is($activities[2]));
    }

    /** @test */
    public function order_by_started_at_prioritises_based_on_stats_preference()
    {
        $activity1 = Activity::factory()->create();
        Stats::factory()->activity($activity1)->create(['integration' => 'php', 'started_at' => Carbon::now()]);

        $activity2 = Activity::factory()->create();
        Stats::factory()->activity($activity2)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(2)]);

        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(1)]);
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'started_at' => Carbon::now()->subDays(5)]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $activities = Activity::orderByStat('started_at')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity3->is($activities[1]));
        $this->assertTrue($activity2->is($activities[2]));

        StatsOrder::setDefaultValue(['strava', 'php']);
        $activities = Activity::orderByStat('started_at')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity2->is($activities[1]));
        $this->assertTrue($activity3->is($activities[2]));
    }

    /** @test */
    public function order_by_started_at_orders_undated_models_first()
    {
        $activity1 = Activity::factory()->create();
        Stats::factory()->activity($activity1)->create(['integration' => 'php', 'started_at' => null]);

        $activity2 = Activity::factory()->create();
        Stats::factory()->activity($activity2)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(2)]);

        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'started_at' => Carbon::now()->subDays(5)]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $activities = Activity::orderByStat('started_at')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity2->is($activities[1]));
        $this->assertTrue($activity3->is($activities[2]));
    }

    /** @test */
    public function order_by_distance_orders_by_distance()
    {
        $activity1 = Activity::factory()->create();
        Stats::factory()->activity($activity1)->create(['distance' => 100]);

        $activity2 = Activity::factory()->create();
        Stats::factory()->activity($activity2)->create(['distance' => 90]);

        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['distance' => 95]);

        $activities = Activity::orderByStat('distance')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity3->is($activities[1]));
        $this->assertTrue($activity2->is($activities[2]));
    }

    /** @test */
    public function order_by_distance_prioritises_based_on_stats_preference()
    {
        $activity1 = Activity::factory()->create();
        Stats::factory()->activity($activity1)->create(['integration' => 'php', 'distance' => 100]);

        $activity2 = Activity::factory()->create();
        Stats::factory()->activity($activity2)->create(['integration' => 'php', 'distance' => 90]);

        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['integration' => 'php', 'distance' => 95]);
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'distance' => 50]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $activities = Activity::orderByStat('distance')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity3->is($activities[1]));
        $this->assertTrue($activity2->is($activities[2]));

        StatsOrder::setDefaultValue(['strava', 'php']);
        $activities = Activity::orderByStat('distance')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity2->is($activities[1]));
        $this->assertTrue($activity3->is($activities[2]));
    }

    /** @test */
    public function order_by_distance_orders_undated_models_first()
    {
        $activity1 = Activity::factory()->create();
        Stats::factory()->activity($activity1)->create(['integration' => 'php', 'distance' => null]);

        $activity2 = Activity::factory()->create();
        Stats::factory()->activity($activity2)->create(['integration' => 'php', 'distance' => 100]);

        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'distance' => 5]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $activities = Activity::orderByStat('distance')->get();
        $this->assertCount(3, $activities);
        $this->assertContainsOnlyInstancesOf(Activity::class, $activities);
        $this->assertTrue($activity1->is($activities[0]));
        $this->assertTrue($activity2->is($activities[1]));
        $this->assertTrue($activity3->is($activities[2]));
    }

    /** @test */
    public function it_appends_the_preferred_distance()
    {
        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['integration' => 'php', 'distance' => 95]);
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'distance' => 50]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $this->assertEquals(95, $activity3->distance);

        StatsOrder::setDefaultValue(['strava', 'php']);
        $this->assertEquals(50, $activity3->distance);
    }

    /** @test */
    public function it_appends_the_preferred_started_at()
    {
        $activity3 = Activity::factory()->create();
        $now = Carbon::now();
        $subDay = Carbon::now()->subDay();
        Stats::factory()->activity($activity3)->create(['integration' => 'php', 'started_at' => $now]);
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'started_at' => $subDay]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $this->assertEquals($now->toIso8601String(), $activity3->started_at->toIso8601String());

        StatsOrder::setDefaultValue(['strava', 'php']);
        $this->assertEquals($subDay->toIso8601String(), $activity3->refresh()->started_at->toIso8601String());
    }

    /** @test */
    public function started_at_attribute_is_null_if_no_stats_available()
    {
        $activity3 = Activity::factory()->create();

        $this->assertNull($activity3->started_at);
    }
}
