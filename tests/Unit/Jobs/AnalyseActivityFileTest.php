<?php

namespace Unit\Jobs;

use App\Jobs\AnalyseActivityFile;
use App\Models\Activity;
use App\Models\File;
use App\Services\Analysis\Analyser\Analyser;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Analyser\AnalysisFactoryContract;
use Prophecy\Argument;
use Tests\TestCase;

class AnalyseActivityFileTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_an_activity_is_missing_a_file()
    {
        $activity = Activity::factory()->create(['file_id' => null]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Activity ' . $activity->id . ' does not have a model associated with it.');

        $job = new AnalyseActivityFile($activity);
        $job->handle();
    }

    /** @test */
    public function it_creates_stats_for_an_activity()
    {
        $activity = Activity::factory()->create([
            'file_id' => File::factory()->routeFile()->create()->id,
        ]);

        $job = new AnalyseActivityFile($activity);
        $job->handle();

        $this->assertDatabaseHas('stats', [
            'stats_id' => $activity->id,
            'stats_type' => Activity::class,
        ]);
    }

    /** @test */
    public function it_maps_stats_across_to_the_model_correctly()
    {
        $analysisResult = (new Analysis());
        $file = File::factory()->routeFile()->create();
        $activity = Activity::factory()->create([
            'file_id' => $file->id,
        ]);

        $analyser = $this->prophesize(AnalysisFactoryContract::class);
        $analyser->analyse(Argument::that(fn ($arg) => $file->is($arg)))->shouldBeCalled()->willReturn($analysisResult);
        $this->app->instance(AnalysisFactoryContract::class, $analyser->reveal());
        Analyser::swap($analyser->reveal());

        $job = new AnalyseActivityFile($activity);
        $job->handle();

        $this->assertDatabaseHas('stats', [
            'stats_id' => $activity->id,
            'stats_type' => Activity::class,
        ]);

        $this->markTestIncomplete('Need to map the stats further');
    }
}
