<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stats;
use App\Settings\StatsOrder;
use Tests\TestCase;

class RouteTest extends TestCase
{

    /** @test */
    public function the_user_id_is_automatically_set_on_creation_if_null(){

    }

    /** @test */
    public function cover_image_returns_the_preview_for_the_file(){

    }

    /** @test */
    public function cover_image_returns_null_if_file_not_set(){

    }

    /** @test */
    public function it_has_a_relationship_with_user(){

    }

    /** @test */
    public function it_has_a_relationship_with_files(){

    }

    /** @test */
    public function it_appends_the_preferred_distance(){
        $route3 = Route::factory()->create();
        Stats::factory()->route($route3)->create(['integration' => 'php', 'distance' => 95]);
        Stats::factory()->route($route3)->create(['integration' => 'strava', 'distance' => 50]);

        StatsOrder::setDefaultValue(['php', 'strava']);
        $this->assertEquals(95, $route3->distance);

        StatsOrder::setDefaultValue(['strava', 'php']);
        $this->assertEquals(50, $route3->distance);
    }

}
