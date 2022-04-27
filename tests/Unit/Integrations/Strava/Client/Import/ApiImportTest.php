<?php

namespace Tests\Unit\Integrations\Strava\Client\Import;

use App\Integrations\Strava\Client\Import\ApiImport;
use App\Integrations\Strava\Client\Import\Resources\Activity;
use Tests\TestCase;

class ApiImportTest extends TestCase
{

    /** @test */
    public function it_loads_an_activity_importer()
    {
        $this->assertInstanceOf(Activity::class, ApiImport::activity());
    }
}
