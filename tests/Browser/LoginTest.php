<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{

    /** @test */
    public function a_user_can_log_in(){
        $user = User::factory()->create([
            'email' => 'example@cycle.test',
            'password' => Hash::make('secret123')
        ]);

        $this->browse(function(Browser $browser) {
            $browser->visit(route('login'))
                    ->type('email', 'example@cycle.test')
                    ->type('password', 'secret123')
                    ->magic();
        });
    }

}
//->assertTitle('Cycle Store');
