<?php

namespace Tests\Unit\Tasks;

use App\Jobs\CreateBackup;
use App\Models\Activity;
use App\Models\File;
use App\Models\Route;
use App\Models\Tour;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use App\Services\Archive\ZipCreatorFactory;
use Carbon\Carbon;
use Prophecy\Argument;
use Tests\TestCase;

class CreateBackupTaskTest extends TestCase
{
    /** @test */
    public function it_creates_an_export_and_creates_a_file()
    {
        $now = Carbon::create(2022, 02, 04, 11, 30, 44);
        Carbon::setTestNow($now);

        $user = User::factory()->create();
        $activities = Activity::factory()->count(5)->create(['user_id' => $user->id]);
        $routes = Route::factory()->count(5)->create(['user_id' => $user->id]);
        $tours = Tour::factory()->count(5)->create(['user_id' => $user->id]);
        $exporter = $this->prophesize(ZipCreatorFactory::class);
        $exporter->start(
            Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user))
        )->shouldBeCalled()->willReturn($exporter->reveal());
        $exporter->add(
            Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user))
        )->shouldBeCalled()->willReturn($exporter->reveal());
        foreach ($activities as $activity) {
            $exporter->add(
                Argument::that(fn ($arg) => $arg instanceof Activity && $arg->is($activity))
            )->shouldBeCalled()->willReturn($exporter->reveal());
        }
        foreach ($tours as $tour) {
            $exporter->add(
                Argument::that(fn ($arg) => $arg instanceof Tour && $arg->is($tour))
            )->shouldBeCalled()->willReturn($exporter->reveal());
        }
        foreach ($routes as $route) {
            $exporter->add(
                Argument::that(fn ($arg) => $arg instanceof Route && $arg->is($route))
            )->shouldBeCalled()->willReturn($exporter->reveal());
        }
        $file = File::factory()->archive()->create(['title' => null, 'caption' => null]);
        $exporter->archive()->shouldBeCalled()->willReturn($file);
        ZipCreator::swap($exporter->reveal());

        CreateBackup::dispatch($user);

        $file->refresh();
        $this->assertEquals('Full backup 04/02/2022', $file->title);
        $this->assertEquals('Full backup taken at 04/02/2022 11:30:44', $file->caption);

//        $task->assertMessages([
//            'Collecting data to back up.',
//            'Added 5 activities.',
//            'Added 5 routes.',
//            'Added 5 tours.',
//            'Generating archive.',
//            'Generated full backup of your data.'
//        ]);
    }
}
