<?php

namespace App\Integrations\Strava\Commands;

use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Console\Command;

class ResetRateLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:reset-limits
                                {--rate : Reset the rate limit}
                                {--daily : Reset the daily rate limit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the strava rate limits';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('rate')) {
            StravaClient::query()->update(['used_15_min_calls' => 0]);
        }
        if ($this->option('daily')) {
            StravaClient::query()->update(['used_daily_calls' => 0]);
        }

        return Command::SUCCESS;
    }
}
