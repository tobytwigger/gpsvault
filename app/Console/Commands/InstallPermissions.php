<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;
use Spatie\Permission\Models\Permission;

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
    public function handle(Repository $config)
    {
        foreach ($config->get('permission.seed', []) as $permission) {
            $this->createPermissionIfMissing($permission);
        }
        foreach (Permission::whereNotIn('name', $config->get('permission.seed', []))->get() as $permission) {
            $permission->delete();
            $this->line('Removed permission ' . $permission->name);
        }
        $this->info('All permissions up to date');

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
