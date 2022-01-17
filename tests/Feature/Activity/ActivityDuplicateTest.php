<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use App\Models\File;
use Tests\TestCase;

class ActivityDuplicateTest extends TestCase
{

    /** @test */
    public function it_identifies_if_a_file_is_not_a_duplicate(){
        $this->authenticated();

        $response = $this->postJson(route('activity.file.duplicate'), ['hash' => '123']);

        $response->assertJsonFragment(['is_duplicate' => false]);
    }

    /** @test */
    public function it_identifies_if_the_file_is_a_duplicate(){
        $this->authenticated();

        $file = File::factory()->activityFile()->create(['user_id' => $this->user->id, 'hash' => 'duplicatedhash']);
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'file_id' => $file->id]);
        $response = $this->postJson(route('activity.file.duplicate'), ['hash' => 'duplicatedhash']);

        $response->assertJsonFragment(['is_duplicate' => true]);
    }

    /** @test */
    public function it_returns_which_file_and_activity_is_the_duplicate(){
        $this->authenticated();

        $file = File::factory()->activityFile()->create(['user_id' => $this->user->id, 'hash' => 'duplicatedhash']);
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'file_id' => $file->id]);
        $response = $this->postJson(route('activity.file.duplicate'), ['hash' => 'duplicatedhash']);

        $response->assertJsonFragment(['is_duplicate' => true]);
        $json = $response->decodeResponseJson();
        $this->assertEquals($json['file']['id'], $file->id);
        $this->assertEquals($json['activity']['id'], $activity->id);
    }

    /** @test */
    public function it_only_checks_your_activities(){
        $this->authenticated();

        $file = File::factory()->activityFile()->create(['user_id' => $this->user->id, 'hash' => 'duplicatedhash']);
        $activity = Activity::factory()->create(['file_id' => $file->id]);
        $response = $this->postJson(route('activity.file.duplicate'), ['hash' => 'duplicatedhash']);

        $response->assertJsonFragment(['is_duplicate' => false]);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $response = $this->postJson(route('activity.file.duplicate'), ['hash' => 'duplicatedhash'])
            ->assertStatus(401);
    }

}
