<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stage;
use App\Models\Tour;
use Tests\TestCase;

class StageTest extends TestCase
{

    /** @test */
    public function it_has_a_relationship_with_a_tour(){
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->create(['tour_id' => $tour]);
        $this->assertTrue($tour->is($stage->tour));
    }

    /** @test */
    public function it_has_a_relationship_with_an_activity(){
        $activity = Activity::factory()->create();
        $stage = Stage::factory()->create(['activity_id' => $activity]);
        $this->assertTrue($activity->is($stage->activity));
    }

    /** @test */
    public function it_has_a_relationship_with_a_route(){
        $route = Route::factory()->create();
        $stage = Stage::factory()->create(['route_id' => $route]);
        $this->assertTrue($route->is($stage->route));
    }

    /** @test */
    public function stage_numbers_are_auto_assigned(){
        $tour = Tour::factory()->create();
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);

        $this->assertEquals(1, $stage1->refresh()->stage_number);
        $this->assertEquals(2, $stage2->refresh()->stage_number);
        $this->assertEquals(3, $stage3->refresh()->stage_number);
        $this->assertEquals(4, $stage4->refresh()->stage_number);
    }

    /** @test */
    public function setStageNumber_sets_the_stage_number_and_reorders_stages(){
        $tour = Tour::factory()->create();
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);

        $this->assertEquals(1, $stage1->refresh()->stage_number);
        $this->assertEquals(2, $stage2->refresh()->stage_number);
        $this->assertEquals(3, $stage3->refresh()->stage_number);
        $this->assertEquals(4, $stage4->refresh()->stage_number);

        $stage2->setStageNumber(4);
        $stage1->setStageNumber(2);

        $this->assertEquals(1, $stage3->refresh()->stage_number);
        $this->assertEquals(2, $stage1->refresh()->stage_number);
        $this->assertEquals(3, $stage4->refresh()->stage_number);
        $this->assertEquals(4, $stage2->refresh()->stage_number);

    }

    /** @test */
    public function deleting_a_stage_reorders_stages_in_the_activity(){
        $tour = Tour::factory()->create();
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);

        $this->assertEquals(1, $stage1->refresh()->stage_number);
        $this->assertEquals(2, $stage2->refresh()->stage_number);
        $this->assertEquals(3, $stage3->refresh()->stage_number);
        $this->assertEquals(4, $stage4->refresh()->stage_number);

        $stage2->delete();

        $this->assertEquals(1, $stage1->refresh()->stage_number);
        $this->assertEquals(2, $stage3->refresh()->stage_number);
        $this->assertEquals(3, $stage4->refresh()->stage_number);
    }

}
