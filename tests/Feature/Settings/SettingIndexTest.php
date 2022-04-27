<?php

namespace Tests\Feature\Settings;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SettingIndexTest extends TestCase
{

    /** @test */
    public function it_loads_the_right_component()
    {
        $this->authenticated();

        $this->get(route('settings.index'))
            ->assertInertia(fn (Assert $page) => $page->component('Settings/Index'));
    }

    /** @test */
    public function you_must_be_logged_in()
    {
        $this->get(route('settings.index'))
            ->assertRedirect(route('login'));
    }
}
