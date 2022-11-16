<?php

namespace Tests\Feature\Public;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    /** @test */
    public function it_loads_the_component()
    {
        $response = $this->get(route('welcome'));

        $response->assertInertia(fn (Assert $page) => $page->component('Public/Welcome'));
    }
}
