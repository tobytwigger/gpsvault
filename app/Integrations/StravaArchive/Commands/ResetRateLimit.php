<?php

namespace App\Integrations\Strava\Commands;

use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\RateLimiter;

class ResetRateLimit extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:ratelimit {limiter : One of minute or day}';

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
        if($this->argument('limiter') === 'minute') {
            StravaClient::query()->update(['used_15_min_calls' => 0]);
        } elseif($this->argument('limiter') === 'day') {
            StravaClient::query()->update(['used_daily_calls' => 0]);
        } else {
            $this->error('Argument must be one of minute or day');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

}
