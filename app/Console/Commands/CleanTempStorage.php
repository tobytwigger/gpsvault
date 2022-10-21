<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CleanTempStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the temporary storage driver of any stale data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $files = collect(Storage::disk('temp')->allFiles())
            ->filter(fn (string $path) => Carbon::createFromTimestamp(Storage::disk('temp')->lastModified($path))->isBefore($this->expiry()))
            ->filter(fn (string $path) => Str::contains($path, '.gitignore'));

        $this->line(sprintf('Removing %u files from temp.', $files->count()));

        Storage::disk('temp')->delete($files->all());

        return Command::SUCCESS;
    }

    protected function expiry()
    {
        return Carbon::now()->subHours(12);
    }
}
