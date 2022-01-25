<?php

namespace Feature\Activity;

use App\Models\Activity;
use App\Models\File;
use App\Services\Archive\Contracts\ZipCreator;
use App\Services\Archive\ZipCreatorFactory;
use Prophecy\Argument;
use Tests\TestCase;

class ActivityDownloadTest extends TestCase
{

    /** @test */
    public function it_returns_a_403_if_you_download_an_activity_that_isnt_yours()
    {
        $this->authenticated();
        $activity = Activity::factory()->create();

        $response = $this->get(route('activity.download', $activity));
        $response->assertStatus(403);
    }

    /** @test */
    public function it_returns_a_404_if_the_activity_does_not_exist(){
        $this->authenticated();

        $response = $this->get(route('activity.download', 3333));
        $response->assertStatus(404);
    }

    /** @test */
    public function it_creates_a_file_and_redirects_to_download_it(){
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id]);
        $file = File::factory()->activityFile()->create();

        $zipFactory = $this->prophesize(ZipCreatorFactory::class);
        $zipFactory->add(Argument::that(fn($arg) => $arg instanceof Activity && $arg->is($activity)))->shouldBeCalled()->willReturn($zipFactory->reveal());
        $zipFactory->archive()->shouldBeCalled()->willReturn($file);
        $this->app->instance(ZipCreatorFactory::class, $zipFactory->reveal());

        $response = $this->get(route('activity.download', $activity));
        $response->assertRedirect(route('file.download', $file));
    }

}
