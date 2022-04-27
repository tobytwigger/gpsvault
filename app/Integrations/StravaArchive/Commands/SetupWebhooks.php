<?php

namespace App\Integrations\Strava\Commands;

use App\Integrations\Strava\Client\Strava;
use App\Models\User;
use Illuminate\Console\Command;

class SetupWebhooks extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:webhooks {--user= : The ID of the user to run against}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the strava webhooks are set up';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Strava $strava)
    {
        // When we have clients, this will handle each client. We can then easily get the user id from the client user id.

        if (!$this->option('user')) {
            $this->warn('The user to run as is not set, skipping.');

            return Command::SUCCESS;
        }

        $user = User::findOrFail((int) $this->option('user'));
        $strava->setUserId($user->id);
        $client = $strava->client($user->availableClient());

        if (!$client->webhookExists()) {
            $this->line('Webhook missing, creating.');
            $client->createWebhook();
        } else {
            $this->line('Webhook already exists');
        }

        return Command::SUCCESS;
    }
}
