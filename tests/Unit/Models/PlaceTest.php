<?php

namespace Tests\Unit\Models;

use App\Jobs\CreateThumbnailImage;
use App\Jobs\GenerateRouteThumbnail;
use App\Models\Place;
use App\Models\Route;
use App\Models\RoutePath;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
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
