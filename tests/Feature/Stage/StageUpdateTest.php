<?php

namespace Tests\Feature\Stage;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Stage;
use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Inertia\Testing\Assert;
use Tests\TestCase;

class StageUpdateTest extends TestCase
{

    /** @test */
    public function it_updates_a_stage_and_shows_the_tour(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $route2 = Route::factory()->create(['user_id' => $this->user->id]);
        $activity2 = Activity::factory()->create(['user_id' => $this->user->id]);

        $stage = Stage::factory()->create([
            'name' => 'Stage name',
            'description' => 'Some description',
            'date' => Carbon::now()->addDay(),
            'is_rest_day' => false,
            'tour_id' => $tour->id,
            'route_id' => $route->id,
            'activity_id' => $activity->id
        ]);

        $response = $this->put(route('tour.stage.update', [$tour, $stage]), [
            'name' => 'Stage name updated',
            'description' => 'Some description updated',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'is_rest_day' => true,
            'route_id' => $route2->id,
            'activity_id' => $activity2->id
        ]);

        $this->assertDatabaseHas('stages', [
            'name' => 'Stage name updated',
            'description' => 'Some description updated',
            'date' => Carbon::now()->addDays(2)->format('Y-m-d 00:00:00'),
            'is_rest_day' => true,
            'route_id' => $route2->id,
            'activity_id' => $activity2->id
        ]);

    }


    /** @test */
    public function the_tour_id_cannot_be_updated(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $tour2 = Tour::factory()->create(['user_id' => $this->user->id]);

        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $response = $this->put(route('tour.stage.update', [$tour, $stage]), ['tour_id' => $tour2->id]);

        $this->assertDatabaseHas('stages', ['tour_id' => $tour->id]);
        $this->assertDatabaseMissing('stages', ['tour_id' => $tour2->id]);
    }

    /** @test */
    public function stages_are_reordered_if_the_stage_number_changes(){
        $this->authenticated();

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 1]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 2]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 3]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 4]);
        $stage5 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 5]);

        $response = $this->put(route('tour.stage.update', [$tour->id, $stage2->id]), ['stage_number' => 4]);

        $this->assertDatabaseHas('stages', ['id' => $stage1->id, 'stage_number' => 1]);
        $this->assertDatabaseHas('stages', ['id' => $stage2->id, 'stage_number' => 4]);
        $this->assertDatabaseHas('stages', ['id' => $stage3->id, 'stage_number' => 2]);
        $this->assertDatabaseHas('stages', ['id' => $stage4->id, 'stage_number' => 3]);
        $this->assertDatabaseHas('stages', ['id' => $stage5->id, 'stage_number' => 5]);
        $this->assertDatabaseHas('stages', ['stage_number' => 5]);
    }

    /** @test */
    public function the_stage_number_is_set_to_the_next_number_available_if_a_higher_number_given(){
        $this->authenticated();

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 1]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 2]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 3]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 4]);
        $stage5 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 5]);

        $response = $this->put(route('tour.stage.update', [$tour->id, $stage2->id]), ['stage_number' => 4000]);

        $this->assertDatabaseHas('stages', ['id' => $stage1->id, 'stage_number' => 1]);
        $this->assertDatabaseHas('stages', ['id' => $stage2->id, 'stage_number' => 5]);
        $this->assertDatabaseHas('stages', ['id' => $stage3->id, 'stage_number' => 2]);
        $this->assertDatabaseHas('stages', ['id' => $stage4->id, 'stage_number' => 3]);
        $this->assertDatabaseHas('stages', ['id' => $stage5->id, 'stage_number' => 4]);
        $this->assertDatabaseHas('stages', ['stage_number' => 5]);
    }

    /** @test */
    public function a_404_response_is_returned_if_the_stage_or_tour_does_not_exist(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->create();
        $response = $this->put(route('tour.stage.update', [$tour, 55]));
        $response->assertStatus(404);

        $response = $this->put(route('tour.stage.update', [100, $stage]));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_404_response_is_returned_if_the_tour_does_not_contain_the_stage(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);
        $stage2 = Stage::factory()->create();

        $response = $this->put(route('tour.stage.update', [$tour, $stage]));
        $response->assertRedirect();

        $response = $this->put(route('tour.stage.update', [$tour, $stage2]));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_403_is_returned_if_you_do_not_own_the_tour(){
        $this->authenticated();
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $response = $this->put(route('tour.stage.update', [$tour, $stage]));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $this->put(route('tour.stage.destroy', [$tour, $stage]))->assertRedirect(route('login'));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates($key, $value, $error){
        $this->authenticated();
        if(is_callable($value)) {
            $value = call_user_func($value, $this->user);
        }
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $response = $this->put(route('tour.stage.update', [$stage->tour_id, $stage]), [$key => $value]);
        if(!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['description', Str::random(100), false],
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', true, 'The name must be a string.'],
            ['description', Str::random(100), false],
            ['description', Str::random(65540), 'The description must not be greater than 65535 characters.'],
            ['description', true, 'The description must be a string.'],
            ['date', 'not-a-date', 'The date does not match the format Y-m-d.'],
            ['date', Carbon::now()->format('d/m/Y'), 'The date does not match the format Y-m-d.'],
            ['date', Carbon::now()->format('Y-m-d H:i:s'), 'The date does not match the format Y-m-d.'],
            ['date', Carbon::now()->format('Y-m-d'), false],
            ['is_rest_day', false, false],
            ['is_rest_day', true, false],
            ['is_rest_day', 'not-a-bool', 'The is rest day field must be true or false.'],
            ['is_rest_day', null, 'The is rest day field must be true or false.'],
            ['route_id','route-id', 'The selected route id is invalid.'],
            ['route_id', 300, 'The selected route id is invalid.'],
            ['route_id', fn() => Route::factory()->create()->id, 'The selected route id is invalid.'],
            ['route_id', fn($user) => Route::factory()->create(['user_id' => $user->id])->id, false],
            ['activity_id','activity-id', 'The selected activity id is invalid.'],
            ['activity_id', 300, 'The selected activity id is invalid.'],
            ['activity_id', fn() => Activity::factory()->create()->id, 'The selected activity id is invalid.'],
            ['activity_id', fn($user) => Activity::factory()->create(['user_id' => $user->id])->id, false],
            ['stage_number', 0, 'The stage number must be at least 1.'],
            ['stage_number', 'not-a-number', 'The stage number must be an integer.']
        ];
    }
}
