<?php

namespace App\Integrations\Strava\Client\Commands;

use App\Integrations\Strava\Client\Models\StravaClient;
use App\Integrations\Strava\Jobs\SyncActivities;
use App\Models\User;
use Illuminate\Console\Command;

class SyncStravaForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:sync {user : ID of the user to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the account of a user with Strava';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::findOrFail($this->argument('user'));

        $this->line('Syncing user ' . $user->email);

        SyncActivities::dispatch($user);

        return Command::SUCCESS;
    }
}
