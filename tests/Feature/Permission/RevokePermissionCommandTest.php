<?php

namespace Tests\Feature\Permission;

use App\Console\Commands\RevokePermissions;
use App\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RevokePermissionCommandTest extends TestCase
{

    /** @test */
    public function it_removes_a_permission_from_a_user()
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'permission-name']);
        $user->givePermissionTo('permission-name');
        $this->assertTrue($user->can('permission-name'));

        $this->artisan(RevokePermissions::class, ['user' => $user->id, 'permission' => 'permission-name'])
            ->expectsOutput('Revoked permission permission-name from the user.')
            ->assertSuccessful();

        $this->assertFalse(User::findOrFail($user->id)->can('permission-name'));
    }

    /** @test */
    public function it_throws_an_exception_if_the_permission_does_not_exist()
    {
        $this->expectException(PermissionDoesNotExist::class);

        $user = User::factory()->create();
        $this->artisan(RevokePermissions::class, ['user' => $user->id, 'permission' => 'permission-name'])
            ->assertFailed();

        $this->assertFalse(User::findOrFail($user->id)->can('permission-name'));
    }

    /** @test */
    public function it_does_nothing_if_the_user_does_not_already_have_the_permission()
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'permission-name']);

        $this->assertFalse($user->can('permission-name'));
        $this->artisan(RevokePermissions::class, ['user' => $user->id, 'permission' => 'permission-name'])
            ->expectsOutput('User does not have the permission.')
            ->assertFailed();

        $this->assertFalse(User::findOrFail($user->id)->can('permission-name'));
    }
}
