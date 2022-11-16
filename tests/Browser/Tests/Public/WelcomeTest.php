<?php

namespace Tests\Browser\Tests\Public;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Public\Welcome;
use Tests\DuskTestCase;

class WelcomeTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     */
    public function test_the_page_loads()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(new Welcome())
                ->assertSee('GPS Vault');
        });
    }
}
