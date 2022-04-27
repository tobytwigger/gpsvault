<?php

namespace Unit\Integrations\Strava\Client\Import\Resources;

use App\Integrations\Strava\Client\Import\Resources\Activity;
use App\Models\Stats;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ActivityTest extends TestCase
{

    /** @test */
    public function it_creates_a_new_activity()
    {
        $this->markTestIncomplete();

        $startDate = Carbon::now()->subDay();
        $user = User::factory()->create();

        $apiData = [
            'id' => 12345,
            'name' => 'Act Name',
            'upload_id_str' => 987654321,
            'total_photo_count' => 5,
            'comment_count' => 2,
            'pr_count' => 3,
            'achievement_count' => 10,
            'kudos_count' => 15,
            'distance' => 5593,
            'start_date' => $startDate->toIso8601String(),
            'elapsed_time' => 1200,
            'average_speed' => 33,
            'elev_low' => 20,
            'elev_high' => 150,
            'total_elevation_gain' => 300,
            'moving_time' => 320,
            'max_speed' => 48,
            'average_cadence' => 98,
            'average_temp' => 22,
            'average_watts' => 129,
            'kilojoules' => 1244,
            'start_latlng' => [01,52],
            'end_latlng' => [02,50],
        ];

        $importer = new Activity();
        $importer->import($apiData, $user);

        /** @var \App\Models\Activity $activity */
        $activity = \App\Models\Activity::firstOrFail();
        $this->assertEquals('Act Name', $activity->name);
        $this->assertEquals($user->id, $activity->user_id);
        $this->assertEquals(['strava'], $activity->linked_to);
        // additional data
        $this->assertEquals(12345, $activity->getAdditionalData('strava_id'));
        $this->assertEquals(987654321, $activity->getAdditionalData('strava_upload_id'));
        $this->assertEquals(5, $activity->getAdditionalData('strava_photo_count'));
        $this->assertEquals(2, $activity->getAdditionalData('strava_comment_count'));
        $this->assertEquals(15, $activity->getAdditionalData('strava_kudos_count'));
        $this->assertEquals(3, $activity->getAdditionalData('strava_pr_count'));
        $this->assertEquals(10, $activity->getAdditionalData('strava_achievement_count'));

        $stats = $activity->statsFrom('strava')->first();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertEquals('strava', $stats->integration);
        $this->assertEquals(5593, $stats->distance);
        $this->assertEquals($startDate->toIso8601String(), $stats->started_at->toIso8601String());
        $this->assertEquals(1200, $stats->duration);
        $this->assertEquals(33, $stats->average_speed);
        $this->assertEquals(20, $stats->min_altitude);
        $this->assertEquals(150, $stats->max_altitude);
        $this->assertEquals(300, $stats->elevation_gain);
        $this->assertEquals(320, $stats->moving_time);
        $this->assertEquals(48, $stats->max_speed);
        $this->assertEquals(98, $stats->average_cadence);
        $this->assertEquals(22, $stats->average_temp);
        $this->assertEquals(129, $stats->average_watts);
        $this->assertEquals(1244, $stats->kilojoules);
        $this->assertEquals(1, $stats->start_latitude);
        $this->assertEquals(52, $stats->start_longitude);
        $this->assertEquals(2, $stats->end_latitude);
        $this->assertEquals(50, $stats->end_longitude);
    }

    /** @test */
    public function it_updates_select_attributes_for_an_activity_linked_to_strava()
    {
        $this->markTestIncomplete();

        $oldUser = User::factory()->create();
        $activity = \App\Models\Activity::factory()->create(['name' => 'Old Name', 'user_id' => $oldUser->id, 'linked_to' => []]);
        $activity->setAdditionalData('strava_id', 12345);

        $stats = Stats::factory()->activity($activity)->create(['integration' => 'strava']);

        $startDate = Carbon::now()->subDay();
        $user = User::factory()->create();

        $apiData = [
            'id' => 12345,
            'name' => 'Act Name',
            'upload_id_str' => 987654321,
            'total_photo_count' => 5,
            'comment_count' => 2,
            'pr_count' => 3,
            'achievement_count' => 10,
            'kudos_count' => 15,
            'distance' => 5593,
            'start_date' => $startDate->toIso8601String(),
            'elapsed_time' => 1200,
            'average_speed' => 33,
            'elev_low' => 20,
            'elev_high' => 150,
            'total_elevation_gain' => 300,
            'moving_time' => 320,
            'max_speed' => 48,
            'average_cadence' => 98,
            'average_temp' => 22,
            'average_watts' => 129,
            'kilojoules' => 1244,
            'start_latlng' => [01,52],
            'end_latlng' => [02,50],
        ];

        $importer = new Activity();
        $importer->import($apiData, $user);

        $activity->refresh();
        $this->assertEquals(['strava'], $activity->linked_to);

        // additional data
        $this->assertEquals(12345, $activity->getAdditionalData('strava_id'));
        $this->assertEquals(987654321, $activity->getAdditionalData('strava_upload_id'));
        $this->assertEquals(5, $activity->getAdditionalData('strava_photo_count'));
        $this->assertEquals(2, $activity->getAdditionalData('strava_comment_count'));
        $this->assertEquals(15, $activity->getAdditionalData('strava_kudos_count'));
        $this->assertEquals(3, $activity->getAdditionalData('strava_pr_count'));
        $this->assertEquals(10, $activity->getAdditionalData('strava_achievement_count'));

        $stats->refresh();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertEquals('strava', $stats->integration);
        $this->assertEquals(5593, $stats->distance);
        $this->assertEquals($startDate->toIso8601String(), $stats->started_at->toIso8601String());
        $this->assertEquals(1200, $stats->duration);
        $this->assertEquals(33, $stats->average_speed);
        $this->assertEquals(20, $stats->min_altitude);
        $this->assertEquals(150, $stats->max_altitude);
        $this->assertEquals(300, $stats->elevation_gain);
        $this->assertEquals(320, $stats->moving_time);
        $this->assertEquals(48, $stats->max_speed);
        $this->assertEquals(98, $stats->average_cadence);
        $this->assertEquals(22, $stats->average_temp);
        $this->assertEquals(129, $stats->average_watts);
        $this->assertEquals(1244, $stats->kilojoules);
        $this->assertEquals(1, $stats->start_latitude);
        $this->assertEquals(52, $stats->start_longitude);
        $this->assertEquals(2, $stats->end_latitude);
        $this->assertEquals(50, $stats->end_longitude);

        $this->assertEquals('Old Name', $activity->name);
        $this->assertEquals($oldUser->id, $activity->user_id);
    }

    /** @test */
    public function it_can_be_created_with_only_an_id()
    {
        $this->markTestIncomplete();

        $user = User::factory()->create();

        $apiData = [
            'id' => 12345
        ];

        $importer = new Activity();
        $importer->import($apiData, $user);

        /** @var \App\Models\Activity $activity */
        $activity = \App\Models\Activity::firstOrFail();
        $this->assertEquals(12345, $activity->getAdditionalData('strava_id'));

        $stats = $activity->statsFrom('strava')->first();
        $this->assertInstanceOf(Stats::class, $stats);
        $this->assertEquals('strava', $stats->integration);
        $this->assertNull($stats->distance);
    }

    /** @test */
    public function it_isnt_marked_as_updated_if_nothing_changes()
    {
        $this->markTestIncomplete();

        $user = User::factory()->create();
        $activity = \App\Models\Activity::factory()->create();
        $activity->setAdditionalData('strava_id', 12345);
        $stats = Stats::factory()->activity($activity)->create(['distance' => 5000.0]);

        $apiData = [
            'id' => 12345,
            'distance' => 5000.0
        ];

        $importer = new Activity();
        $importer->import($apiData, $user);

        $this->assertNull($importer->status());
    }

    /** @test */
    public function it_is_marked_as_updated_if_updated()
    {
        $this->markTestIncomplete();

        $user = User::factory()->create();
        $activity = \App\Models\Activity::factory()->create();
        $activity->setAdditionalData('strava_id', 12345);
        $stats = Stats::factory()->activity($activity)->create(['distance' => 5000]);

        $apiData = [
            'id' => 12345,
            'distance' => 6000
        ];

        $importer = new Activity();
        $importer->import($apiData, $user);

        $this->assertEquals(Activity::UPDATED, $importer->status());
    }

    /** @test */
    public function it_is_marked_as_created_if_created()
    {
        $this->markTestIncomplete();

        $user = User::factory()->create();
        $activity = \App\Models\Activity::factory()->create();

        $stats = Stats::factory()->activity($activity)->create(['distance' => 5000]);

        $apiData = [
            'id' => 12345,
            'distance' => 6000.0
        ];

        $importer = new Activity();
        $importer->import($apiData, $user);

        $this->assertEquals(Activity::CREATED, $importer->status());
    }

    /** @test */
    public function it_updates_an_activity_with_a_similar_distance_and_start_time()
    {
        $this->markTestIncomplete();
    }
}
