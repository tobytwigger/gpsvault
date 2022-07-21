<?php

namespace Feature\Route\Planner;

use App\Models\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlannerEditTest extends TestCase
{

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('planner.edit', Route::factory()->create()->id))->assertRedirect(route('login'));
    }

    /** @test */
    public function it_loads_the_correct_page()
    {
        $this->authenticated();

        $route = Route::factory()->create(['user_id' => $this->user->id]);

        $this->get(route('planner.edit', $route->id))->assertInertia(fn (Assert $page) =>
            $page->component('Route/Planner')
                ->has('routeModel', fn (Assert $page) => $page
                    ->where('id', $route->id)
                    ->where('description', $route->description)
                    ->where('notes', $route->notes)
                    ->where('user_id', $this->user->id)
                    ->etc()
                ));
    }

    /** @test */
    public function you_can_only_load_your_own_routes()
    {
        $this->authenticated();

        $route = Route::factory()->create();

        $this->get(route('planner.edit', $route->id))->assertStatus(403);
    }

}
