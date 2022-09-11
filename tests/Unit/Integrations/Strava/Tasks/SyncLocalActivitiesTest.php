<?php

namespace Unit\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Client\Resources\Activity as ActivityClientResource;
use App\Integrations\Strava\Client\StravaClientFactory;
use App\Integrations\Strava\Import\Resources\Activity as ActivityImporter;
use App\Models\User;
use Prophecy\Argument;
use Tests\TestCase;
use Tests\Utils\MocksStrava;

class SyncLocalActivitiesTest extends TestCase
{
    use MocksStrava;

    /** @test */
    public function it_paginates_activities()
    {
        $this->markTestIncomplete();
        $user = User::factory()->create();

        $stravaActivityClient = $this->prophesize(ActivityClientResource::class);
        $stravaActivityClient->getActivities(1)->willReturn([
            ['id' => 1],
            ['id' => 2],
        ]);
        $stravaActivityClient->getActivities(2)->willReturn([
            ['id' => 3],
        ]);
        $stravaActivityClient->getActivities(3)->willReturn([]);
        $stravaActivityClient->getActivities(4)->shouldNotBeCalled();
        $this->mockResource('activity', $stravaActivityClient->reveal());
        $this->app->instance(StravaClientFactory::class, $this->stravaFactory($user));

        $importer = $this->prophesize(ActivityImporter::class);
        $importer->import(['id' => 1], Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))->shouldBeCalled()->willReturn($importer->reveal());
        $importer->import(['id' => 2], Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))->shouldBeCalled()->willReturn($importer->reveal());
        $importer->import(['id' => 3], Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))->shouldBeCalled()->willReturn($importer->reveal());
        $importer->status()->willReturn(ActivityImporter::CREATED);
        $this->app->instance(ActivityImporter::class, $importer->reveal());

        \App\Integrations\Strava\Jobs\SyncActivities::dispatch($user);
    }

    /** @test */
    public function it_gives_feedback_during_execution()
    {
        $this->markTestIncomplete();
        $user = User::factory()->create();

        $stravaActivityClient = $this->prophesize(ActivityClientResource::class);
        $stravaActivityClient->getActivities(1)->willReturn([
            ['id' => 1],
            ['id' => 2],
        ]);
        $stravaActivityClient->getActivities(2)->willReturn([
            ['id' => 3],
        ]);
        $stravaActivityClient->getActivities(3)->willReturn([]);
        $stravaActivityClient->getActivities(4)->shouldNotBeCalled();
        $this->mockResource('activity', $stravaActivityClient->reveal());
        $this->app->instance(StravaClientFactory::class, $this->stravaFactory($user));

        $importer = $this->prophesize(ActivityImporter::class);
        $importer->import(['id' => 1], Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))->shouldBeCalled()->willReturn($importer->reveal());
        $importer->import(['id' => 2], Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))->shouldBeCalled()->willReturn($importer->reveal());
        $importer->import(['id' => 3], Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))->shouldBeCalled()->willReturn($importer->reveal());
        $importer->status()->willReturn(ActivityImporter::CREATED, ActivityImporter::UPDATED, ActivityImporter::CREATED);
        $this->app->instance(ActivityImporter::class, $importer->reveal());

        \App\Integrations\Strava\Jobs\SyncActivities::dispatch($user);

//        $task->assertMessages([
//            'Importing activities 0 to 2',
//            'Importing activities 2 to 3',
//            'Found 3 activities, including 2 new and 1 updated.'
//        ]);
    }
}
