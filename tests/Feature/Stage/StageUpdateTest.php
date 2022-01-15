<?php

namespace Tests\Feature\Stage;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stage;
use App\Models\Tour;
use Carbon\Carbon;
use Inertia\Testing\Assert;
use Tests\TestCase;

class StageUpdateTest extends TestCase
{

    /** @test */
    public function it_updates_a_stage_and_shows_the_tour(){
        $this->authenticated();
        $tour = Tour::factory()->create();
        $route = Route::factory()->create();
        $activity = Activity::factory()->create();
        $route2 = Route::factory()->create();
        $activity2 = Activity::factory()->create();

        $stage = Stage::factory()->create([
            'name' => 'Stage name',
            'description' => 'Some description',
            'date' => Carbon::now()->addDay(),
            'is_rest_day' => false,
            'tour_id' => $tour->id,
            'route_id' => $route->id,
            'activity_id' => $activity->id
        ]);

        $response = $this->put(route('stage.update', $stage), [
            'name' => 'Stage name updated',
            'description' => 'Some description updated',
            'date' => Carbon::now()->addDays(2),
            'is_rest_day' => true,
            'route_id' => $route2->id,
            'activity_id' => $activity2->id
        ]);

        $this->assertDatabaseHas('stages', [
            'name' => 'Stage name updated',
            'description' => 'Some description updated',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
            'is_rest_day' => true,
            'route_id' => $route2->id,
            'activity_id' => $activity2->id
        ]);

    }


    /** @test */
    public function the_tour_id_cannot_be_updated(){
        $this->authenticated();
        $tour = Tour::factory()->create();
        $tour2 = Tour::factory()->create();

        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $response = $this->put(route('stage.update', $stage), ['tour_id' => $tour2->id]);

        $this->assertDatabaseHas('stages', ['tour_id' => $tour->id]);
        $this->assertDatabaseMissing('stages', ['tour_id' => $tour2->id]);
    }

    /** @test */
    public function stages_are_reordered_if_the_stage_id_changes(){
        $this->authenticated();

        $tour = Tour::factory()->create();
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 1]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 2]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 3]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 4]);
        $stage5 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 5]);

        $response = $this->put(route('stage.update', $stage2->id), ['stage_number' => 4]);

        $this->assertDatabaseHas('stages', ['id' => $stage1->id, 'stage_number' => 1]);
        $this->assertDatabaseHas('stages', ['id' => $stage2->id, 'stage_number' => 4]);
        $this->assertDatabaseHas('stages', ['id' => $stage3->id, 'stage_number' => 2]);
        $this->assertDatabaseHas('stages', ['id' => $stage4->id, 'stage_number' => 3]);
        $this->assertDatabaseHas('stages', ['id' => $stage5->id, 'stage_number' => 5]);
        $this->assertDatabaseHas('stages', ['stage_number' => 5]);
    }

    /** @test */
    public function a_404_response_is_returned_if_the_stage_does_not_exist(){
        $this->authenticated();

        $response = $this->put(route('stage.update', 55));

        $response->assertStatus(404);
    }

}
