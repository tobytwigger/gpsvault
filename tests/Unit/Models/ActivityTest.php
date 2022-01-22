<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Stats;
use App\Settings\StatsOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActivityTest extends TestCase
{

    /** @test */
    public function the_user_id_is_automatically_set_on_creation_if_null(){

    }

    /** @test */
    public function it_deletes_related_models(){

    }

    /** @test */
    public function cover_image_returns_the_preview_for_the_file(){

    }

    /** @test */
    public function cover_image_returns_null_if_file_not_set(){

    }

    /** @test */
    public function it_has_a_relationship_with_strava_comments(){

    }

    /** @test */
    public function it_has_a_relationship_with_strava_kudos(){

    }

    /** @test */
    public function it_has_a_relationship_with_user(){

    }

    /** @test */
    public function it_has_a_relationship_with_files(){

    }

    /** @test */
    public function it_can_be_linked_to_an_activity_and_queried_as_such(){

    }

    /** @test */
    public function orderByStartedAt_orders_by_started_at(){
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
    public function orderByStartedAt_prioritises_based_on_stats_preference(){
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
    public function orderByStartedAt_orders_undated_models_first(){
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
    public function orderByDistance_orders_by_distance(){
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
    public function orderByDistance_prioritises_based_on_stats_preference(){
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
    public function orderByDistance_orders_undated_models_first(){
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
    public function it_appends_the_preferred_distance(){
        $activity3 = Activity::factory()->create();
        Stats::factory()->activity($activity3)->create(['integration' => 'php', 'distance' => 95]);
        Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'distance' => 50]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $this->assertEquals(95, $activity3->distance);

        StatsOrder::setDefaultValue(['strava', 'php']);
        $this->assertEquals(50, $activity3->distance);
    }

    /** @test */
    public function it_appends_the_preferred_started_at(){
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
    public function started_at_attribute_is_null_if_no_stats_available(){
        $activity3 = Activity::factory()->create();

        $this->assertNull($activity3->started_at);
    }
}
