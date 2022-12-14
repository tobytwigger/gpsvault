<?php

namespace Tests\Feature\Route;

use App\Models\Route;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RouteIndexTest extends TestCase
{
    /** @test */
    public function index_loads_the_component()
    {
        $this->authenticated();

        $this->get(route('route.index'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Route/Index')
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('route.index'))->assertRedirect(route('login'));
    }
}
