<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\ApiImport;
use App\Integrations\Strava\Models\StravaComment;
use Carbon\Carbon;

class LoadStravaComments extends StravaBaseJob
{
    public function alias(): ?string
    {
        return 'load-strava-comments';
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $strava = Strava::client($this->activity->user);
        $page = 1;
        do {
            $comments = $strava->activity()->getComments($this->activity->getAdditionalData('strava_id'), $page);
            foreach ($comments as $comment) {
                ApiImport::comment()->import($comment, $this->activity);
            }
            $page++;
        } while (count($comments) === 200);
    }

}
