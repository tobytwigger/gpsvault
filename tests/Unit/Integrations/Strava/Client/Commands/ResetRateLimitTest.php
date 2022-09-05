<?php

namespace Tests\Unit\Integrations\Strava\Client\Commands;

use App\Integrations\Strava\Client\Models\StravaClient;
use Tests\TestCase;

class ResetRateLimitTest extends TestCase
{
    /** @test */
    public function it_resets_the_15_minute_limit()
    {
        $client1 = StravaClient::factory()->create(['used_15_min_calls' => 87, 'used_daily_calls' => 870]);
        $client2 = StravaClient::factory()->create(['used_15_min_calls' => 150, 'used_daily_calls' => 1500]);
        $client3 = StravaClient::factory()->create(['used_15_min_calls' => 0, 'used_daily_calls' => 0]);

        $this->artisan('strava:reset-limits --rate')
            ->assertSuccessful();

        $this->assertEquals(0, $client1->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client2->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client3->refresh()->used_15_min_calls);
        $this->assertEquals(870, $client1->refresh()->used_daily_calls);
        $this->assertEquals(1500, $client2->refresh()->used_daily_calls);
        $this->assertEquals(0, $client3->refresh()->used_daily_calls);
    }

    /** @test */
    public function it_resets_the_daily_limit()
    {
        $client1 = StravaClient::factory()->create(['used_15_min_calls' => 87, 'used_daily_calls' => 870]);
        $client2 = StravaClient::factory()->create(['used_15_min_calls' => 150, 'used_daily_calls' => 1500]);
        $client3 = StravaClient::factory()->create(['used_15_min_calls' => 0, 'used_daily_calls' => 0]);

        $this->artisan('strava:reset-limits --daily')
            ->assertSuccessful();

        $this->assertEquals(87, $client1->refresh()->used_15_min_calls);
        $this->assertEquals(150, $client2->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client3->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client1->refresh()->used_daily_calls);
        $this->assertEquals(0, $client2->refresh()->used_daily_calls);
        $this->assertEquals(0, $client3->refresh()->used_daily_calls);
    }

    /** @test */
    public function it_resets_both()
    {
        $client1 = StravaClient::factory()->create(['used_15_min_calls' => 87, 'used_daily_calls' => 870]);
        $client2 = StravaClient::factory()->create(['used_15_min_calls' => 150, 'used_daily_calls' => 1500]);
        $client3 = StravaClient::factory()->create(['used_15_min_calls' => 0, 'used_daily_calls' => 0]);

        $this->artisan('strava:reset-limits --daily --rate')
            ->assertSuccessful();

        $this->assertEquals(0, $client1->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client2->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client3->refresh()->used_15_min_calls);
        $this->assertEquals(0, $client1->refresh()->used_daily_calls);
        $this->assertEquals(0, $client2->refresh()->used_daily_calls);
        $this->assertEquals(0, $client3->refresh()->used_daily_calls);
    }
}
