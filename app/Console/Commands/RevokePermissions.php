<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class RevokePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:revoke {user} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke permissions from a user.';

    /**
     * Create a new command instance.
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
        if (!$user->hasPermissionTo($permission)) {
            $this->error('User does not have the permission.');

            return Command::FAILURE;
        }
        $user->revokePermissionTo($permission);
        $this->info('Revoked permission ' . $permission->name . ' from the user.');

        return 0;
    }

    protected function createPermissionIfMissing(string $name): void
    {
        if (!Permission::where(['name' => $name])->exists()) {
            Permission::create(['name' => $name]);
            $this->line('Created permission ' . $name);
        }
    }
}
