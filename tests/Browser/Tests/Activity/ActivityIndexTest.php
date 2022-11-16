<?php

namespace Tests\Browser\Tests\Activity;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Activity\Index;
use Tests\Browser\Pages\Public\Welcome;
use Tests\DuskTestCase;

class ActivityIndexTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     */
    public function test_the_page_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::factory()->create())
                ->visit(new Index())
                ->assertSee('Your Activities');
        });
    }
}
