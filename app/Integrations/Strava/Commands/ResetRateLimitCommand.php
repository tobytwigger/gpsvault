<?php

namespace App\Integrations\Strava\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\RateLimiter;

class ResetRateLimitCommand extends Command
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
            RateLimiter::clear(md5('stravastrava-15-mins'));
        } elseif($this->argument('limiter') === 'day') {
            RateLimiter::clear(md5('stravastrava-daily'));
        } else {
            $this->error('Argument must be one of minute or day');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

}
