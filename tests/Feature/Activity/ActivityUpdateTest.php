<?php

namespace Tests\Feature\Activity;

use Illuminate\Support\Str;
use Tests\TestCase;

class ActivityUpdateTest extends TestCase
{

    /** @test */
    public function an_activity_can_be_updated(){
        $this->authenticated();

    }

    /** @test */
    public function it_redirects_to_show_the_updated_activity(){
        $this->authenticated();

    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates($key, $value, $error){
//        $this->authenticated();
//        if(is_callable($value)) {
//            $value = call_user_func($value, $this->user);
//        }
//        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
//        $stage = Stage::factory()->create(['tour_id' => $tour->id]);
//
//        $response = $this->put(route('tour.stage.update', [$stage->tour_id, $stage]), [$key => $value]);
//        if(!$error) {
//            $response->assertSessionHasNoErrors();
//        } else {
//            $response->assertSessionHasErrors([$key => $error]);
//        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
//            ['name', true, 'The name must be a string.'],
//            ['file', 'route-id', 'The selected route id is invalid.'],
        ];
    }

    /** @test */
    public function you_must_be_authenticated(){

    }

    /** @test */
    public function you_can_only_update_your_own_activity(){

    }
}
