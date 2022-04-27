<?php

namespace Feature\Route;

use App\Models\File;
use App\Models\Route;
use App\Services\Archive\ZipCreatorFactory;
use Prophecy\Argument;
use Tests\TestCase;

class RouteDownloadTest extends TestCase
{
    public function it_returns_a_403_if_you_download_an_route_that_isnt_yours()
    {
        $this->authenticated();
        $route = Route::factory()->create();

        $response = $this->get(route('route.download', $route));
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_route_does_not_exist()
    {
        $this->authenticated();

        $response = $this->get(route('route.download', 3333));
        $response->assertStatus(404);
    }

    /** @test */
    public function it_creates_a_file_and_redirects_to_download_it()
    {
        $this->authenticated();
        $route = Route::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->routeFile()->create();

        $zipFactory = $this->prophesize(ZipCreatorFactory::class);
        $zipFactory->add(Argument::that(fn ($arg) => $arg instanceof Route && $arg->is($route)))->shouldBeCalled()->willReturn($zipFactory->reveal());
        $zipFactory->archive()->shouldBeCalled()->willReturn($file);
        $this->app->instance(ZipCreatorFactory::class, $zipFactory->reveal());

        $response = $this->get(route('route.download', $route));
        $response->assertRedirect(route('file.download', $file));
    }
}
