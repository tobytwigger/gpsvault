<?php

namespace App\Integrations\Strava\Commands;

use App\Integrations\Strava\Jobs\SaveNewStravaActivities;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class SyncStravaCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:sync {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Strava';

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
     * @return mixed
     */
    public function handle()
    {
        $user = User::findOrFail($this->argument('user'));
        Auth::login($user);
        SaveNewStravaActivities::dispatchSync($user);
    }

}
