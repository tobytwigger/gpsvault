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

    protected $permissions = [
        'manage-global-settings',
        'manage-strava-clients',
        'use-public-strava-clients'
    ];

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
        foreach($this->permissions as $permission) {
            $this->createPermissionIfMissing($permission);
        }
        $this->info('All permissions up to date');
        return 0;
    }

    protected function createPermissionIfMissing(string $name): void
    {
        if(!Permission::where(['name' => $name])->exists()) {
            Permission::create(['name' => $name]);
            $this->line('Created permission ' . $name);
        }
    }
}
