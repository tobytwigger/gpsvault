<?php

namespace Tests\Feature\Stats;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Analysis\Parser\Point;
use Tests\TestCase;

class GeoJsonTest extends TestCase
{
    /** @test */
    public function you_get_a_list_of_points()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        $stats = Stats::factory()->activity($activity)->create();

        $points = [
            (new Point())->setLatitude(1)->setLongitude(50)->setSpeed(20),
            (new Point())->setLatitude(2)->setLongitude(51)->setSpeed(21),
            (new Point())->setLatitude(3)->setLongitude(52)->setSpeed(22),
            (new Point())->setLatitude(4)->setLongitude(53)->setSpeed(23),
        ];
        $stats->waypoints()->createMany(collect($points)->map(fn (Point $point) => [
            'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
            'speed' => $point->getSpeed(),
        ]));

        $response = $this->getJson(route('stats.geojson', $stats));

        $response->assertStatus(200);
        $response->assertJson([
            'type' => 'LineString',
            'coordinates' => [
                [50,1],[51,2],[52,3],[53,4],
            ],
        ]);
    }

    /** @test */
    public function you_can_only_see_stats_for_your_activity()
    {
        $this->authenticated();

        $activity = Activity::factory()->create();
        $stats = Stats::factory()->activity($activity)->create();

        $response = $this->getJson(route('stats.geojson', $stats));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activity = Activity::factory()->create();
        $stats = Stats::factory()->activity($activity)->create();

        $response = $this->getJson(route('stats.geojson', $stats));
        $response->assertStatus(401);
    }

    /** @test */
    public function an_empty_array_is_returned_if_no_waypoints_exist()
    {
        $this->authenticated();

        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $stats = Stats::factory()->activity($activity)->create();

        $response = $this->getJson(route('stats.geojson', $stats));
        $this->assertEquals(json_encode([
            'type' => 'LineString',
            'coordinates' => [],
        ]), $response->content());
    }
}
