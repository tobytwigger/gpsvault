<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GrantPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:grant {user} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give permissions to a user.';

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
        $user = User::findOrFail($this->argument('user'));
        $permission = Permission::findByName($this->argument('permission'));
        $user->givePermissionTo($permission);
        $this->info('Granted user the permission');
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
