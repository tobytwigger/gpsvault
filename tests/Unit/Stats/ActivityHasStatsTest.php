<?php

namespace Tests\Unit\Stats;

use App\Jobs\AnalyseActivityFile;
use App\Models\Activity;
use App\Models\File;
use App\Models\Stats;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ActivityHasStatsTest extends TestCase
{
    /** @test */
    public function stats_can_be_attached_to_an_activity()
    {
        $activity = Activity::factory()->create();
        $stat = Stats::factory()->activity($activity)->create();

        $this->assertCount(1, $activity->stats()->get());
        $this->assertTrue($stat->is($activity->stats()->first()));
    }

    /** @test */
    public function an_activity_can_be_retrieved_from_the_stats()
    {
        $activity = Activity::factory()->create();
        $stat = Stats::factory()->activity($activity)->create();

        $this->assertTrue($stat->model()->exists());
        $this->assertTrue($activity->is($stat->model));
    }

    /** @test */
    public function the_file_can_be_retrieved()
    {
        $file = File::factory()->activityFile()->create();
        $activity = Activity::factory()->create();

        $this->assertFalse($activity->hasFile());

        $activity->file_id = $file->id;
        $activity->save();

        $this->assertInstanceOf(File::class, $activity->file);
        $this->assertTrue($file->is($activity->file));

        $this->assertTrue($activity->hasFile());
    }

    /** @test */
    public function we_can_scope_to_only_activities_missing_a_file()
    {
        Activity::factory()->create(['file_id' => File::factory()->activityFile()->create()->id]);
        Activity::factory()->count(5)->create(['file_id' => null]);

        $results = Activity::withoutFile()->get();
        $this->assertCount(5, $results);
    }

    /** @test */
    public function the_analyse_file_job_can_be_dispatched()
    {
        Bus::fake(AnalyseActivityFile::class);

        $activity = Activity::factory()->create(['file_id' => File::factory()->activityFile()->create()->id]);
        $activity->analyse();

        Bus::assertDispatched(AnalyseActivityFile::class, fn (AnalyseActivityFile $job) => $activity->is($job->model));
    }

    /** @test */
    public function it_can_scope_stats_from_an_integration()
    {
        $file = File::factory()->activityFile()->create();
        $activity = Activity::factory()->create();
        $stravaStats = Stats::factory()->activity($activity)->create(['integration' => 'strava']);
        $ownStats = Stats::factory()->activity($activity)->create(['integration' => 'php']);

        $this->assertInstanceOf(Stats::class, $activity->statsFrom('php')->first());
        $this->assertTrue($ownStats->is($activity->statsFrom('php')->first()));
        $this->assertInstanceOf(Stats::class, $activity->statsFrom('strava')->first());
        $this->assertTrue($stravaStats->is($activity->statsFrom('strava')->first()));
    }

    /** @test */
    public function it_deletes_stats_when_the_activity_is_deleted()
    {
        $activity = Activity::factory()->create();
        $stats = Stats::factory()->activity($activity)->count(2)->create();

        $this->assertDatabaseCount('stats', 2);
        $activity->delete();
        $this->assertDatabaseCount('stats', 0);
    }
}
