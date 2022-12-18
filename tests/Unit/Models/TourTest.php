<?php

namespace Tests\Unit\Models;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\Route;
use App\Models\RoutePath;
use App\Models\Stage;
use App\Models\Tour;
use App\Services\Geocoding\Geocoder;
use Illuminate\Support\Facades\Bus;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;
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
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->has(
            RoutePath::factory()->state(fn ($attributes) => ['distance' => 50000])
        )->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->has(
            RoutePath::factory()->state(fn ($attributes) => ['distance' => 79333])
        )->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $this->assertEquals(129333, $tour->distance);
    }

    /** @test */
    public function it_appends_the_elevation_gain()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->has(
            RoutePath::factory()->state(fn ($attributes) => ['elevation_gain' => 1000])
        )->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 =         Route::factory()->has(
            RoutePath::factory()->state(fn ($attributes) => ['elevation_gain' => 500])
        )->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $this->assertEquals(1500, $tour->elevation_gain);
    }

    /** @test */
    public function it_appends_the_distance_and_ignores_any_stages_without_a_route()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->has(
            RoutePath::factory()->state(fn ($attributes) => ['distance' => 50000])
        )->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->has(
            RoutePath::factory()->state(fn ($attributes) => ['distance' => 79333])
        )->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => null]);

        $this->assertEquals(129333, $tour->distance);
    }

    /** @test */
    public function human_started_at_returns_the_started_at_attribute_from_geocoder()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        RoutePath::factory()->route($route1)->create(['linestring' => new LineString([new Point(1, 51, 0), new Point(2, 52, 2)])]);
        RoutePath::factory()->route($route2)->create(['linestring' => new LineString([new Point(3, 53, 1), new Point(2, 52, 3)])]);

        $this->assertEquals('Milton Keynes, UK', $tour->human_started_at);
    }

    /** @test */
    public function human_started_at_returns_null_if_no_stages_have_a_path()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $this->assertNull($tour->human_started_at);
    }

    /** @test */
    public function human_ended_at_returns_the_ended_at_attribute_from_geocoder()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(4, 54)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        RoutePath::factory()->route($route1)->create(['linestring' => new LineString([new Point(1, 51, 0), new Point(2, 52, 2)])]);
        RoutePath::factory()->route($route2)->create(['linestring' => new LineString([new Point(3, 53, 1), new Point(4, 54, 3)])]);

        $this->assertEquals('Milton Keynes, UK', $tour->human_ended_at);
    }

    /** @test */
    public function human_ended_at_returns_null_if_no_stages_have_a_path()
    {
        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(1, 51)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $this->assertNull($tour->human_ended_at);
    }

    /** @test */
    public function human_ended_at_uses_the_latest_stage_with_a_route_path()
    {
        Bus::fake([GenerateRouteThumbnail::class, CreateThumbnailImage::class]);

        $geocoder = $this->prophesize(Geocoder::class);
        $geocoder->getPlaceSummaryFromPosition(2, 52)->willReturn('Milton Keynes, UK');
        $this->app->instance(Geocoder::class, $geocoder->reveal());

        $tour = Tour::factory()->create();
        $route1 = Route::factory()->has(
            RoutePath::factory()->state(['linestring' => new LineString([new Point(1, 51, 2), new Point(2, 52, 4)])])
        )->create();
        $route2 = Route::factory()->create();
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route1->id]);
        Stage::factory()->create(['tour_id' => $tour->id, 'route_id' => $route2->id]);

        $this->assertEquals('Milton Keynes, UK', $tour->human_ended_at);
    }
}
