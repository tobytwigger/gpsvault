<?php

namespace App\Console\Commands;

use App\Jobs\AnalyseActivityFile;
use App\Models\Activity;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class AnalyseActivityFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:analyse {activity? : The ID of the activity to parse}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $activities = $this->argument('activity') ? Arr::wrap(Activity::findOrFail($this->argument('activity'))) : Activity::get();
        foreach($activities as $activity) {
            AnalyseActivityFile::dispatch($activity);
        }
        return Command::SUCCESS;
    }
}
