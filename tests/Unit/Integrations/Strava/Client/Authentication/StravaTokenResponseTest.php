<?php

namespace Unit\Integrations\Strava\Client\Authentication;

use App\Integrations\Strava\Client\Authentication\StravaTokenResponse;
use Carbon\Carbon;
use Tests\TestCase;

class StravaTokenResponseTest extends TestCase
{
    /** @test */
    public function it_can_be_created_with_values()
    {
        $expiresAt = Carbon::now()->addHour();

        $response = StravaTokenResponse::create(
            $expiresAt,
            500,
            'refresh-token',
            'access-token',
            10
        );

        $this->assertEquals($expiresAt->toIso8601String(), Carbon::make($response->getExpiresAt())->toIso8601String());
        $this->assertEquals(500, $response->getExpiresIn());
        $this->assertEquals('refresh-token', $response->getRefreshToken());
        $this->assertEquals('access-token', $response->getAccessToken());
        $this->assertEquals(10, $response->getAthleteId());
    }

    /** @test */
    public function it_can_be_created_with_setters()
    {
        $expiresAt = Carbon::now()->addHour();

        $response = new StravaTokenResponse();
        $response->setExpiresAt($expiresAt);
        $response->setExpiresIn(500);
        $response->setRefreshToken('refresh-token');
        $response->setAccessToken('access-token');
        $response->setAthleteId(10);

        $response = StravaTokenResponse::create(
            $expiresAt,
            500,
            'refresh-token',
            'access-token',
            10
        );

        $this->assertEquals($expiresAt->toIso8601String(), Carbon::make($response->getExpiresAt())->toIso8601String());
        $this->assertEquals(500, $response->getExpiresIn());
        $this->assertEquals('refresh-token', $response->getRefreshToken());
        $this->assertEquals('access-token', $response->getAccessToken());
        $this->assertEquals(10, $response->getAthleteId());
    }
}
