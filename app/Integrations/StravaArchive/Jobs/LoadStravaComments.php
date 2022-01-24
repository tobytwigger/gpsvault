<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Models\StravaComment;
use Carbon\Carbon;

class LoadStravaComments extends StravaActivityBaseJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Strava $strava)
    {
        $strava->setUserId($this->activity->user_id);
        $page = 1;
        do {
            $comments = $strava->client($this->stravaClientModel)->getComments($this->activity->getAdditionalData('strava_id'), $page);
            foreach($comments as $comment) {
                $this->importComment($comment);
            }
            $page++;
        } while (count($comments) === 200);

        $this->activity->setAdditionalData('strava_is_loading_comments', false);
    }

    private function importComment(mixed $comment)
    {
        StravaComment::updateOrCreate(
            ['strava_id' => $comment['id']],
            [
                'first_name' => $comment['athlete']['firstname'],
                'last_name' => $comment['athlete']['lastname'],
                'activity_id' => $this->activity->id,
                'text' => $comment['text'],
                'posted_at' => Carbon::make($comment['created_at'])
            ]
        );
    }

}
