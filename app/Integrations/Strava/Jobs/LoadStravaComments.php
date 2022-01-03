<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Models\StravaComment;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

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
            $comments = $strava->client($this->activity->user->availableClient())->getComments($this->activity->getAdditionalData('strava_id'), $page);
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
