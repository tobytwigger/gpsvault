<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\File;
use App\Models\Stats;
use App\Services\Geocoding\Geocoder;
use App\Settings\StatsOrder;
use Carbon\Carbon;
use Tests\TestCase;

class StatsTest extends TestCase
{

    /** @test */
    public function it_has_a_relationship_to_a_model(){
        $activity = Activity::factory()->create();
        $stats = Stats::factory()->activity($activity)->create();

        $this->assertTrue($activity->is($stats->model));
    }

    /** @test */
    public function get_human_started_at_gets_the_human_name_for_the_started_at_location(){
        $stats = Stats::factory()->activity(Activity::factory()->create())->create();
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition($stats->start_latitude, $stats->start_longitude)
            ->willReturn('StartSummary');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $this->assertEquals('StartSummary', $stats->human_started_at);
    }

    /** @test */
    public function get_human_ended_at_gets_the_human_name_for_the_ended_at_location(){
        $stats = Stats::factory()->activity(Activity::factory()->create())->create();
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition($stats->end_latitude, $stats->end_longitude)
            ->willReturn('EndSummary');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $this->assertEquals('EndSummary', $stats->human_ended_at);
    }

    /** @test */
    public function get_human_started_at_returns_null_until_the_start_lat_and_long_are_set(){
        $stats = Stats::factory()->activity(Activity::factory()->create())->create(['start_latitude' => null, 'start_longitude' => null]);
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('StartSummary');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $this->assertNull($stats->human_started_at);

        $stats->start_latitude = 1;
        $stats->save();
        $this->assertNull($stats->human_started_at);

        $stats->start_latitude = null;
        $stats->start_longitude = 51;
        $stats->save();
        $this->assertNull($stats->human_started_at);

        $stats->start_latitude = 1;
        $stats->save();
        $this->assertEquals('StartSummary', $stats->human_started_at);
    }

    /** @test */
    public function get_human_ended_at_returns_null_until_the_end_lat_and_long_are_set(){
        $stats = Stats::factory()->activity(Activity::factory()->create())->create(['end_latitude' => null, 'end_longitude' => null]);
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('EndSummary');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $this->assertNull($stats->human_ended_at);

        $stats->end_latitude = 1;
        $stats->save();
        $this->assertNull($stats->human_ended_at);

        $stats->end_latitude = null;
        $stats->end_longitude = 51;
        $stats->save();
        $this->assertNull($stats->human_ended_at);

        $stats->end_latitude = 1;
        $stats->save();
        $this->assertEquals('EndSummary', $stats->human_ended_at);
    }

    /** @test */
    public function orderByPreference_orders_stats_by_preference(){
        $activity1 = Activity::factory()->create();
        $stats1 = Stats::factory()->activity($activity1)->create(['integration' => 'php', 'started_at' => Carbon::now()]);

        $activity2 = Activity::factory()->create();
        $stats2 = Stats::factory()->activity($activity2)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(2)]);

        $activity3 = Activity::factory()->create();
        $stats3 = Stats::factory()->activity($activity3)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(1)]);
        $stats4 = Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'started_at' => Carbon::now()->subDays(5)]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $stats = Stats::orderByPreference()->get();
        $this->assertCount(4, $stats);
        $this->assertContainsOnlyInstancesOf(Stats::class, $stats);
        $this->assertTrue($stats1->is($stats[0]));
        $this->assertTrue($stats2->is($stats[1]));
        $this->assertTrue($stats3->is($stats[2]));
        $this->assertTrue($stats4->is($stats[3]));

        StatsOrder::setDefaultValue(['strava', 'php']);
        $stats = Stats::orderByPreference()->get();
        $this->assertCount(4, $stats);
        $this->assertContainsOnlyInstancesOf(Stats::class, $stats);
        $this->assertTrue($stats4->is($stats[0]));
        $this->assertTrue($stats1->is($stats[1]));
        $this->assertTrue($stats2->is($stats[2]));
        $this->assertTrue($stats3->is($stats[3]));
    }

    /** @test */
    public function preferred_returns_the_preferred_stats()
    {
        $activity1 = Activity::factory()->create();
        $stats1 = Stats::factory()->activity($activity1)->create(['integration' => 'php', 'started_at' => Carbon::now()]);

        $activity2 = Activity::factory()->create();
        $stats2 = Stats::factory()->activity($activity2)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(2)]);

        $activity3 = Activity::factory()->create();
        $stats3 = Stats::factory()->activity($activity3)->create(['integration' => 'php', 'started_at' => Carbon::now()->subDays(1)]);
        $stats4 = Stats::factory()->activity($activity3)->create(['integration' => 'strava', 'started_at' => Carbon::now()->subDays(5)]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $stats = $activity1->stats()->preferred()->first();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertTrue($stats1->is($stats));

        $stats = $activity3->stats()->preferred()->first();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertTrue($stats3->is($stats));

        StatsOrder::setDefaultValue(['strava', 'php']);

        $stats = $activity1->stats()->preferred()->first();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertTrue($stats1->is($stats));

        $stats = $activity3->stats()->preferred()->first();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertTrue($stats4->is($stats));
    }

}
