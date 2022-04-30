<?php

namespace App\Console\Commands;

use App\Jobs\AnalyseFile;
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
        $activities = $this->argument('activity') ? Arr::wrap(Activity::findOrFail($this->argument('activity'))) : Activity::get();
        foreach ($activities as $activity) {
            AnalyseFile::dispatch($activity);
            $count++;
        }
        $this->line(sprintf('Set %u activities for analysis.', $count));

        return Command::SUCCESS;
    }
}
