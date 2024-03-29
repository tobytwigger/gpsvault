<?php

namespace Tests\Feature\Stats;

use App\Models\Activity;
use App\Models\Stats;
use App\Services\Analysis\Parser\Point;
use Tests\TestCase;

class PointsTest extends TestCase
{
    public function todo_scaffolding_mark_sure_this_controller_is_tested_since_refactor()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function you_get_a_list_of_points()
    {
        $this->markTestIncomplete();
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $stats = Stats::factory()->activity($activity)->create();

        $points = [
            (new Point())->setLatitude(1)->setLongitude(50)->setSpeed(20),
            (new Point())->setLatitude(2)->setLongitude(51)->setSpeed(21),
            (new Point())->setLatitude(3)->setLongitude(52)->setSpeed(22),
            (new Point())->setLatitude(4)->setLongitude(53)->setSpeed(23),
        ];
        $stats->activityPoints()->createMany(collect($points)->map(fn (Point $point) => [
            'points' => new \MStaack\LaravelPostgis\Geometries\Point($point->getLatitude(), $point->getLongitude()),
            'speed' => $point->getSpeed(),
        ]));

        $response = $this->getJson(route('stats.points', $stats));
        $response->assertStatus(200);
        $response->assertJson([
            ['latitude' => 1, 'longitude' => 50, 'speed' => 20],
            ['latitude' => 2, 'longitude' => 51, 'speed' => 21],
            ['latitude' => 3, 'longitude' => 52, 'speed' => 22],
            ['latitude' => 4, 'longitude' => 53, 'speed' => 23],
        ]);
    }

    /** @test */
    public function you_can_only_see_stats_for_your_activity()
    {
        $this->authenticated();

        $activity = Activity::factory()->create();
        $stats = Stats::factory()->activity($activity)->create();

        $response = $this->getJson(route('stats.points', $stats));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activity = Activity::factory()->create();
        $stats = Stats::factory()->activity($activity)->create();

        $response = $this->getJson(route('stats.points', $stats));
        $response->assertStatus(401);
    }

    /** @test */
    public function an_empty_array_is_returned_if_no_activity_points_exist()
    {
        $this->authenticated();

        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $stats = Stats::factory()->activity($activity)->create();

        $response = $this->getJson(route('stats.points', $stats));
        $this->assertEquals('[]', $response->content());
    }
}
