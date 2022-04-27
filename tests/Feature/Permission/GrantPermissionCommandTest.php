<?php

namespace Tests\Feature\Permission;

use App\Console\Commands\GrantPermissions;
use App\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GrantPermissionCommandTest extends TestCase
{

    /** @test */
    public function it_gives_the_user_a_permission()
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'permission-name']);

        $this->assertFalse($user->can('permission-name'));

        $this->artisan(GrantPermissions::class, ['user' => $user->id, 'permission' => 'permission-name'])
            ->expectsOutput('Granted user the permission.')
            ->assertSuccessful();

        $this->assertTrue(User::findOrFail($user->id)->can('permission-name'));
    }

    /** @test */
    public function it_throws_an_exception_if_the_permission_does_not_exist()
    {
        $this->expectException(PermissionDoesNotExist::class);

        $user = User::factory()->create();
        $this->artisan(GrantPermissions::class, ['user' => $user->id, 'permission' => 'permission-name'])
            ->assertFailed();

        $this->assertFalse(User::findOrFail($user->id)->can('permission-name'));
    }

    /** @test */
    public function it_does_nothing_if_the_user_already_has_the_permission()
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'permission-name']);
        $user->givePermissionTo('permission-name');

        $this->assertTrue($user->can('permission-name'));

        $this->artisan(GrantPermissions::class, ['user' => $user->id, 'permission' => 'permission-name'])
            ->expectsOutput('User already has the permission.')
            ->assertFailed();

        $this->assertTrue(User::findOrFail($user->id)->can('permission-name'));
    }
}
