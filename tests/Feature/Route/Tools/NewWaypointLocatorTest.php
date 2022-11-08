<?php

namespace Tests\Feature\Route\Tools;

use App\Models\User;
use Tests\TestCase;

class NewWaypointLocatorTest extends TestCase
{
    /** @test */
    public function todo_scaffolding()
    {
        $this->markTestSkipped();
    }

    /**
     * @test
     *  @dataProvider  indexesAndLines
     */
    public function it_generates_the_index(array $line, array $point, int $index){
        $this->be(User::factory()->create());

        $response = $this->post(route('planner.tools.new-waypoint-locator'), [
            'geojson' => $line,
            'lat' => $point['lat'],
            'lng' => $point['lng']
        ]);

        $response->assertJson(['index' => $index]);
    }

    public function indexesAndLines(): array
    {
        return [
            [
                [
                    ['lat' => 52.04092173773364, 'lng' => -0.8166350278101788],
                    ['lat' => 52.03199383368809, 'lng' => -0.7620573415898662],
                    ['lat' => 52.05710546137706, 'lng' => 0.7375122340096425]
                ],
                ['lat' => 52.05128906728575, 'lng' => -0.750203850611797],
                2
            ],
        ];
    }

}
