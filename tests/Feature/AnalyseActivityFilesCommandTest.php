<?php

namespace Tests\Feature;

use App\Console\Commands\AnalyseActivityFiles;
use App\Jobs\AnalyseActivityFile;
use App\Models\Activity;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AnalyseActivityFilesCommandTest extends TestCase
{
    /** @test */
    public function it_fires_an_analysis_job_for_the_given_activity()
    {
        Bus::fake(AnalyseActivityFile::class);

        $activity = Activity::factory()->create();
        Activity::factory()->count(10)->create();

        $this->artisan(AnalyseActivityFiles::class, ['activity' => $activity->id])
            ->expectsOutput('Set 1 activities for analysis.')
            ->assertSuccessful();

        Bus::assertDispatched(AnalyseActivityFile::class, fn (AnalyseActivityFile $job) => $job->activity->is($activity));
    }

    /** @test */
    public function it_fires_an_analysis_job_for_all_activities_if_no_id_given()
    {
        Bus::fake(AnalyseActivityFile::class);

        $activities = Activity::factory()->count(10)->create();

        $this->artisan(AnalyseActivityFiles::class)
            ->expectsOutput('Set 10 activities for analysis.')
            ->assertSuccessful();

        Bus::assertDispatchedTimes(AnalyseActivityFile::class, 10);
        foreach ($activities as $activity) {
            Bus::assertDispatched(AnalyseActivityFile::class, fn (AnalyseActivityFile $job) => $job->activity->is($activity));
        }
    }
}
