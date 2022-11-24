<?php

namespace Tests;

use App\Models\User;

class DocumentationGenerator extends DuskTestCase
{
    private User $user;

    public function user(): User
    {
        if (!isset($this->user)) {
            $this->user = User::factory()->create([
                'name' => 'Toby Twigger',
            ]);
        }

        return $this->user;
    }
}
