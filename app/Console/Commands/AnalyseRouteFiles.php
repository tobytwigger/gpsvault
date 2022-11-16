<?php

namespace App\Console\Commands;

use App\Jobs\AnalyseRouteFile;
use App\Models\Route;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class AnalyseRouteFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routing:analyse {route? : The ID of the route to parse}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $count = 0;
        $activities = $this->argument('route') ? Arr::wrap(Route::findOrFail($this->argument('route'))) : Route::get();
        foreach ($activities as $route) {
            AnalyseRouteFile::dispatch($route);
            $count++;
        }
        $this->line(sprintf('Set %u routes for analysis.', $count));

        return Command::SUCCESS;
    }
}
