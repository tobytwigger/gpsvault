<?php

namespace Tests\Feature;

use App\Console\Commands\AnalyseActivityFiles;
use App\Jobs\AnalyseFile;
use App\Models\Activity;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AnalyseActivityFilesCommandTest extends TestCase
{

    /** @test */
    public function it_fires_an_analysis_job_for_the_given_activity(){
        Bus::fake(AnalyseFile::class);

        $activity = Activity::factory()->create();
        Activity::factory()->count(10)->create();

        $this->artisan(AnalyseActivityFiles::class, ['activity' => $activity->id])
            ->expectsOutput('Set 1 activities for analysis.')
            ->assertSuccessful();

        Bus::assertDispatched(AnalyseFile::class, fn(AnalyseFile $job) => $job->model->is($activity));
    }

    /** @test */
    public function it_fires_an_analysis_job_for_all_activities_if_no_id_given(){
        Bus::fake(AnalyseFile::class);

        $activities = Activity::factory()->count(10)->create();

        $this->artisan(AnalyseActivityFiles::class)
            ->expectsOutput('Set 10 activities for analysis.')
            ->assertSuccessful();

        Bus::assertDispatchedTimes(AnalyseFile::class, 10);
        foreach($activities as $activity) {
            Bus::assertDispatched(AnalyseFile::class, fn(AnalyseFile $job) => $job->model->is($activity));
        }
    }

}
