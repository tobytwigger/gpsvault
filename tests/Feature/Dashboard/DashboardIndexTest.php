<?php

namespace Tests\Feature\Dashboard;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardIndexTest extends TestCase
{
    /** @test */
    public function it_returns_the_dashboard_component()
    {
        $this->authenticated();

        $this->get(route('dashboard'))
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard/Dashboard'));
    }

    /** @test */
    public function todo_scaffolding()
    {
        $this->markTestIncomplete('Add tests for creating default dashboards, viewing a dashboard etc.');
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
