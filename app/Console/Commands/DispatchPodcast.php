<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPodcast;
use Illuminate\Bus\Queueable;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchPodcast extends Command implements ShouldQueue
{
    use Queueable, Dispatchable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:dispatch {--fail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProcessPodcast::dispatch(!$this->option('fail'))->delay(now()->addSeconds(3));

        return 0;
    }
}
