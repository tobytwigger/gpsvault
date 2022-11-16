<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class PromoteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:promote {user : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give a user permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var User $user */
        $user = User::findOrFail($this->argument('user'));
        $user->givePermissionTo(config('permission.seed', []));
//        dd();
//        foreach(config('permissions.seed', []) as $permissionName) {
//            dd(Permission::findByName($permissionName));
//        }
        $this->line('Gave user permissions');
        return 0;
    }
}
