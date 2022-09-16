<?php

namespace Tests\Unit\Integrations\Strava\Import;

use App\Integrations\Strava\Import\Api\ApiImport;
use App\Integrations\Strava\Import\Api\Resources\Activity;
use App\Integrations\Strava\Import\Api\Resources\Comment;
use App\Integrations\Strava\Import\Api\Resources\Kudos;
use App\Integrations\Strava\Import\Api\Resources\LimitedActivity;
use App\Integrations\Strava\Import\Api\Resources\Photos;
use App\Integrations\Strava\Import\Api\Resources\Stats;
use Tests\TestCase;

class ApiImportTest extends TestCase
{
    /** @test */
    public function it_loads_an_activity_importer()
    {
        $this->assertInstanceOf(Activity::class, ApiImport::activity());
    }

    /** @test */
    public function it_loads_a_limited_activity_importer()
    {
        $this->assertInstanceOf(LimitedActivity::class, ApiImport::limitedActivity());
    }

    /** @test */
    public function it_loads_a_comment_importer()
    {
        $this->assertInstanceOf(Comment::class, ApiImport::comment());
    }

    /** @test */
    public function it_loads_a_kudos_importer()
    {
        $this->assertInstanceOf(Kudos::class, ApiImport::kudos());
    }

    /** @test */
    public function it_loads_a_photos_importer()
    {
        $this->assertInstanceOf(Photos::class, ApiImport::photos());
    }

    /** @test */
    public function it_loads_a_stats_importer()
    {
        $this->assertInstanceOf(Stats::class, ApiImport::stats());
    }
}
