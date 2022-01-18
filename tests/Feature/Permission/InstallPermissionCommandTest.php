<?php

namespace Tests\Feature\Permission;

use App\Console\Commands\GrantPermissions;
use App\Console\Commands\InstallPermissions;
use App\Models\User;
use Illuminate\Contracts\Config\Repository;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class InstallPermissionCommandTest extends TestCase
{

    /** @test */
    public function it_installs_all_permissions(){
        $config = $this->prophesize(Repository::class);
        $config->get('permission.seed', [])->willReturn(['per-one', 'per-two', 'per-three']);
        $this->instance(Repository::class, $config->reveal());

        $this->artisan(InstallPermissions::class)
            ->expectsOutput('Created permission per-one')
            ->expectsOutput('Created permission per-two')
            ->expectsOutput('Created permission per-three')
            ->assertSuccessful();

        $this->assertTrue(Permission::where('name', 'per-one')->exists());
        $this->assertTrue(Permission::where('name', 'per-two')->exists());
        $this->assertTrue(Permission::where('name', 'per-three')->exists());
    }

    /** @test */
    public function it_installs_new_permissions(){
        $config = $this->prophesize(Repository::class);
        $config->get('permission.seed', [])->willReturn(['per-one', 'per-two', 'per-three']);
        $this->instance(Repository::class, $config->reveal());

        Permission::create(['name' => 'per-one']);

        $this->artisan(InstallPermissions::class)
            ->expectsOutput('Created permission per-two')
            ->expectsOutput('Created permission per-three')
            ->assertSuccessful();

        $this->assertTrue(Permission::where('name', 'per-one')->exists());
        $this->assertTrue(Permission::where('name', 'per-two')->exists());
        $this->assertTrue(Permission::where('name', 'per-three')->exists());
    }

    /** @test */
    public function it_can_remove_permissions(){
        $config = $this->prophesize(Repository::class);
        $config->get('permission.seed', [])->willReturn(['per-two', 'per-three']);
        $this->instance(Repository::class, $config->reveal());
        Permission::create(['name' => 'per-one']);

        $this->artisan(InstallPermissions::class)
            ->expectsOutput('Created permission per-two')
            ->expectsOutput('Created permission per-three')
            ->expectsOutput('Removed permission per-one')
            ->assertSuccessful();

        $this->assertFalse(Permission::where('name', 'per-one')->exists());
        $this->assertTrue(Permission::where('name', 'per-two')->exists());
        $this->assertTrue(Permission::where('name', 'per-three')->exists());
    }


}
