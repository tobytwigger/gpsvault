<?php

namespace Unit\Services\Stats\Addition;

use App\Models\Stats;
use App\Services\Stats\Addition\StatAdder;
use Tests\TestCase;

class StatAdderTest extends TestCase
{

    /** @test */
    public function it_adds_the_distance_of_many_stats(){
        $stat1 = Stats::factory()->activity()->create(['distance' => 50000]);
        $stat2 = Stats::factory()->activity()->create(['distance' => 79333]);
        $stat3 = Stats::factory()->activity()->create(['distance' => 25933]);

        $adder = new StatAdder([$stat1, $stat2, $stat3]);

        $this->assertEquals(155266, $adder->distance());
    }

    /** @test */
    public function it_adds_the_elevation_of_many_stats(){
        $stat1 = Stats::factory()->activity()->create(['elevation_gain' => 1000]);
        $stat2 = Stats::factory()->activity()->create(['elevation_gain' => 500]);
        $stat3 = Stats::factory()->activity()->create(['elevation_gain' => 179]);

        $adder = new StatAdder([$stat1, $stat2, $stat3]);

        $this->assertEquals(1679, $adder->elevationGain());
    }

    /** @test */
    public function push_pushes_extra_stats_onto_the_adder(){
        $stat1 = Stats::factory()->activity()->create(['distance' => 50000]);
        $stat2 = Stats::factory()->activity()->create(['distance' => 79333]);
        $stat3 = Stats::factory()->activity()->create(['distance' => 25933]);
        $stat4 = Stats::factory()->activity()->create(['distance' => 10]);

        $adder = new StatAdder([$stat1, $stat2]);
        $adder->push($stat3);
        $adder->push($stat4);

        $this->assertEquals(155276, $adder->distance());
    }

    /**
     * @test
     */
    public function it_returns_null_when_there_are_no_stats(){
        $stats = Stats::factory()->activity()->count(2)->create(['distance' => null, 'elevation_gain' => null]);

        $adder = new StatAdder($stats->all());
        $this->assertNull($adder->distance());
        $this->assertNull($adder->elevationGain());
    }

    /** @test */
    public function toArray_returns_an_array_of_properties(){

        $stat1 = Stats::factory()->activity()->create(['distance' => 50000, 'elevation_gain' => 1000]);
        $stat2 = Stats::factory()->activity()->create(['distance' => 79333, 'elevation_gain' => 500]);
        $stat3 = Stats::factory()->activity()->create(['distance' => 25933, 'elevation_gain' => 179]);

        $adder = new StatAdder([$stat1, $stat2, $stat3]);

        $this->assertEquals([
            'distance' => 155266,
            'elevation_gain' => 1679
        ], $adder->toArray());
    }

}
