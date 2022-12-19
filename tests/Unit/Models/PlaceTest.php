<?php

namespace Tests\Unit\Models;

use App\Models\Place;
use App\Models\User;
use Tests\TestCase;

class PlaceTest extends TestCase
{
    public function it_has_a_user()
    {
        $user = User::factory()->create();
        $place = Place::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->is($place->user));
    }
}
