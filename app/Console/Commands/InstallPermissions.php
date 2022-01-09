<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InstallPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the permissions needed for this site.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $adminRole = Role::findByName('Admin');
        } catch (RoleDoesNotExist $e) {
            $adminRole = Role::create(['name' => 'Admin']);
        }
        if(!Permission::where(['name' => 'manage-global-settings'])->exists()) {
            Permission::create(['name' => 'manage-global-settings']);
            $adminRole->givePermissionTo('manage-global-settings');
        }
        if(!Permission::where(['name' => 'manage-strava-clients'])->exists()) {
            Permission::create(['name' => 'manage-strava-clients']);
        }
        return 0;
    }
}
