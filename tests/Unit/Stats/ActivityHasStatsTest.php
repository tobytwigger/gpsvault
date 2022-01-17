<?php

namespace Tests\Unit\Stats;

use App\Models\Activity;
use App\Models\File;
use App\Models\Stats;
use Tests\TestCase;

class ActivityHasStatsTest extends TestCase
{

    /** @test */
    public function stats_can_be_attached_to_an_activity(){
        $file = File::factory()->activityFile()->create();
        $activity = Activity::factory()->create(['default_stats_id' => null]);
        $stat1 = Stats::factory()->create(['file_id' => $file->id]);
        $stat2 = Stats::factory()->create(['file_id' => $file->id]);

        $this->assertCount(1, $activity->stats);
        $this->assertTrue($stat->is($activity->stats[0]));
    }

    /** @test */
    public function creating_a_stat_with_an_activity_file_changes_the_default_stat_id(){
        $activity = Activity::factory()->create(['default_stats_id' => null]);
        $file = File::factory()->activityFile()->create();

        $stats = Stats::factory()->create([
            'file_id' => $file->id,
        ]);

        $this->assertEquals($stats->id, $activity->refresh()->default_stats_id);
    }
}
