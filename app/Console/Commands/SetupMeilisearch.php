<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;
use MeiliSearch\Client;
use MeiliSearch\Exceptions\InvalidResponseBodyException;

class SetupMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install meilisearch';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(config('scout.driver') === 'meilisearch') {
            $client = app(Client::class);
            $client->index('activities')->updateSortableAttributes(['updated_at']);
            $client->index('activities')->updateFilterableAttributes(['user_id']);
        } else {
            $this->line('Skipping');
        }
    }
}
