<?php

namespace Tests\Unit\Models;

use App\Models\Route;
use App\Models\Stage;
use App\Models\Stats;
use App\Models\Tour;
use App\Services\Geocoding\Geocoder;
use Tests\TestCase;

class TourTest extends TestCase
{

    /** @test */
    public function it_has_a_relationship_to_stages()
    {
        $tour = Tour::factory()->create();
        $stages = Stage::factory()->count(5)->create(['tour_id' => $tour->id, 'stage_number' => null]);

        $this->assertContainsOnlyInstancesOf(Stage::class, $tour->stages);
        foreach ($tour->stages as $stage) {
            $this->assertTrue($stages->shift()->is($stage));
        }
    }

    /** @test */
    public function stages_are_ordered()
    {
        $tour = Tour::factory()->create();
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2->setStageNumber(4);
        $stage1->setStageNumber(2);

        $this->assertContainsOnlyInstancesOf(Stage::class, $tour->stages);
        $this->assertTrue($tour->stages[0]->is($stage3));
        $this->assertTrue($tour->stages[1]->is($stage1));
        $this->assertTrue($tour->stages[2]->is($stage4));
        $this->assertTrue($tour->stages[3]->is($stage2));
    }

    /** @test */
    public function it_adds_the_user_id_when_created()
    {
        $this->authenticated();
        $tour = Tour::factory()->make(['user_id' => null]);
        $tour->save();

        $this->assertEquals($this->user->id, $tour->refresh()->user_id);
    }

    /** @test */
    public function it_appends_the_distance()
    {
        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $stat1 = Stats::factory()->route($route1)->create(['distance' => 50000]);
        $stat2 = Stats::factory()->route($route2)->create(['distance' => 79333]);

        $this->assertEquals(129333, $tour->distance);
    }

    /** @test */
    public function it_appends_the_elevation_gain()
    {
        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $stat1 = Stats::factory()->route($route1)->create(['elevation_gain' => 1000]);
        $stat2 = Stats::factory()->route($route2)->create(['elevation_gain' => 500]);

        $this->assertEquals(1500, $tour->elevation_gain);
    }

    /** @test */
    public function the_stats_can_be_loaded()
    {
        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $stat1 = Stats::factory()->route($route1)->create(['distance' => 50000, 'elevation_gain' => 1000, 'start_latitude' => 1, 'start_longitude' => 2]);
        $stat2 = Stats::factory()->route($route2)->create(['distance' => 79333, 'elevation_gain' => 500, 'end_latitude' => 3, 'end_longitude' => 4]);

        $this->assertEquals([
            'distance' => 129333,
            'elevation_gain' => 1500,
            'start_latitude' => 1,
            'start_longitude' => 2,
            'end_latitude' => 3,
            'end_longitude' => 4,
        ], $tour->stats);
    }

    /** @test */
    public function it_appends_the_distance_and_ignores_any_stages_without_a_route()
    {
        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => null]);

        $stat1 = Stats::factory()->route($route1)->create(['distance' => 50000]);
        $stat2 = Stats::factory()->route($route2)->create(['distance' => 79333]);

        $this->assertEquals(129333, $tour->distance);
    }

    /** @test */
    public function human_started_at_returns_the_started_at_attribute_from_geocoder()
    {
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $stat1 = Stats::factory()->route($route1)->create(['start_latitude' => 1, 'start_longitude' => 51]);
        $stat1 = Stats::factory()->route($route2)->create(['end_latitude' => 2, 'end_longitude' => 54]);

        $this->assertEquals('Milton Keynes, UK', $tour->human_started_at);
    }

    /** @test */
    public function human_started_at_returns_null_if_stats_have_start_latitude_or_longitude_as_null()
    {
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);

        $stat1 = Stats::factory()->route($route1)->create(['start_latitude' => null, 'start_longitude' => null]);

        $this->assertNull($tour->human_started_at);

        $stat1->start_latitude = 1;
        $stat1->save();
        $this->assertNull($tour->refresh()->human_started_at);

        $stat1->start_longitude = 51;
        $stat1->start_latitude = null;
        $stat1->save();
        $this->assertNull($tour->refresh()->human_started_at);

        $stat1->start_latitude = 1;
        $stat1->save();

        $this->assertEquals('Milton Keynes, UK', $tour->human_started_at);
    }

    /** @test */
    public function human_ended_at_returns_the_ended_at_attribute_from_geocoder()
    {
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(2, 54)->willReturn('Oxford, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $stat1 = Stats::factory()->route($route1)->create(['start_latitude' => 1, 'start_longitude' => 51]);
        $stat1 = Stats::factory()->route($route2)->create(['end_latitude' => 2, 'end_longitude' => 54]);

        $this->assertEquals('Oxford, UK', $tour->human_ended_at);
    }

    /** @test */
    public function human_ended_at_returns_null_if_stats_have_end_latitude_or_longitude_as_null()
    {
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);

        $stat1 = Stats::factory()->route($route1)->create(['end_latitude' => null, 'end_longitude' => null]);

        $this->assertNull($tour->human_ended_at);

        $stat1->end_latitude = 1;
        $stat1->save();
        $this->assertNull($tour->refresh()->human_ended_at);

        $stat1->end_longitude = 51;
        $stat1->end_latitude = null;
        $stat1->save();
        $this->assertNull($tour->refresh()->human_ended_at);

        $stat1->end_latitude = 1;
        $stat1->save();

        $this->assertEquals('Milton Keynes, UK', $tour->human_ended_at);
    }
}
