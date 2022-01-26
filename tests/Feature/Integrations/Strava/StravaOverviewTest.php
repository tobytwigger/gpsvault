<?php

namespace Feature\Integrations\Strava;

use Inertia\Testing\Assert;
use Tests\TestCase;

class StravaOverviewTest extends TestCase
{

    /** @test */
    public function it_loads_the_inertia_component(){
        $this->authenticated();

        $response = $this->get(route('integration.strava'));
        $response->assertStatus(200);
        $response->assertInertia(fn(Assert $page) => $page
            ->component('Integrations/Strava/Index')
        );
    }

    /** @test */
    public function you_must_be_authenticated(){
        $response = $this->get(route('integration.strava'));
        $response->assertRedirect(route('login'));
    }

}
