<?php

namespace Tests\Unit\Models;

use App\Models\Stage;
use App\Models\Tour;
use Tests\TestCase;

class TourTest extends TestCase
{

    /** @test */
    public function it_has_a_relationship_to_stages(){
        $tour = Tour::factory()->create();
        $stages = Stage::factory()->count(5)->create(['tour_id' => $tour->id, 'stage_number' => null]);

        $this->assertContainsOnlyInstancesOf(Stage::class, $tour->stages);
        foreach($tour->stages as $stage) {
            $this->assertTrue($stages->shift()->is($stage));
        }
    }

    /** @test */
    public function stages_are_ordered(){
        $tour = Tour::factory()->create();
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => null]);
        $stage2->setStageNumber(4);
        $stage1->setStageNumber(2);

        $this->assertContainsOnlyInstancesOf(Stage::class, $tour->stages);
        $this->assertTrue($tour->stages[0]->is($stage3));
        $this->assertTrue($tour->stages[1]->is($stage1));
        $this->assertTrue($tour->stages[2]->is($stage4));
        $this->assertTrue($tour->stages[3]->is($stage2));
    }

    /** @test */
    public function it_adds_the_user_id_when_created(){
        $this->authenticated();
        $tour = Tour::factory()->make(['user_id' => null]);
        $tour->save();

        $this->assertEquals($this->user->id, $tour->refresh()->user_id);
    }

}
