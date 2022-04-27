<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\File;
use App\Models\Route;
use App\Models\User;
use App\Services\Sync\Sync;
use Tests\TestCase;

class UserTest extends TestCase
{

    /** @test */
    public function it_deletes_related_models_on_delete()
    {
        $user = User::factory()->create();

        $user->activities()->delete();
        $user->files()->delete();

        $syncs = Sync::factory()->count(5)->create(['user_id' => $user->id]);
        $activities = Activity::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertDatabaseCount('syncs', 5);
        $this->assertDatabaseCount('activities', 5);

        $user->delete();

        $this->assertDatabaseCount('syncs', 0);
        $this->assertDatabaseCount('activities', 0);
    }

    /** @test */
    public function it_has_a_relationship_to_syncs()
    {
        $user = User::factory()->create();
        $syncs = Sync::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertContainsOnlyInstancesOf(Sync::class, $user->syncs);
        $retrievedSyncs = $user->syncs;
        foreach ($syncs as $sync) {
            $this->assertTrue($sync->is($retrievedSyncs->shift()));
        }
    }

    /** @test */
    public function it_has_a_relationship_to_activities()
    {
        $user = User::factory()->create();
        $activities = Activity::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertContainsOnlyInstancesOf(Activity::class, $user->activities);
        $retrievedActivities = $user->activities;
        foreach ($activities as $activity) {
            $this->assertTrue($activity->is($retrievedActivities->shift()));
        }
    }

    /** @test */
    public function it_has_a_relationship_to_routes()
    {
        $user = User::factory()->create();
        $routes = Route::factory()->count(5)->create(['user_id' => $user->id]);

        $this->assertContainsOnlyInstancesOf(Route::class, $user->routes);
        $retrievedRoutes = $user->routes;
        foreach ($routes as $route) {
            $this->assertTrue($route->is($retrievedRoutes->shift()));
        }
    }

    /** @test */
    public function it_has_a_relationship_to_files()
    {
        $user = User::factory()->create();
        $files = File::factory()->activityPoints()->count(5)->create(['user_id' => $user->id]);
        $files2 = File::factory()->routeMedia()->count(5)->create(['user_id' => $user->id]);

        $this->assertContainsOnlyInstancesOf(File::class, $user->files);
        $this->assertCount(10, $user->files);
        $retrievedFiles = $user->files;
        foreach ($files as $file) {
            $this->assertTrue($file->is($retrievedFiles->shift()));
        }
        foreach ($files2 as $file) {
            $this->assertTrue($file->is($retrievedFiles->shift()));
        }
    }
}
