<?php

namespace App\Integrations\Strava\Listeners;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Integrations\Strava\Models\StravaComment;
use App\Models\Activity;
use Carbon\Carbon;
use Composer\Command\AboutCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class IndexStravaActivityComments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->queue = 'indexing';
        $this->strava = $strava;
    }

    /**
     * Determine whether the listener should be queued.
     *
     * @param StravaActivityUpdated $activityEvent
     * @return bool
     */
    public function shouldQueue(StravaActivityUpdated $activityEvent)
    {
        return in_array('strava', $activityEvent->activity->linked_to);
    }

    public function handle(StravaActivityUpdated $activityEvent)
    {
        $activity = $activityEvent->activity->refresh();
        $this->strava->setUserId($activity->user_id);
        $page = 1;
        do {
            $comments = $this->strava->client()->getComments($activity->getAdditionalData('strava_id'), $page);
            foreach($comments as $comment) {
                $this->importComment($activity, $comment);
            }
            $page++;
        } while (count($comments) === 200);

        $activity->setAdditionalData('strava_is_loading_comments', false);
    }

    public function middleware()
    {
        return [
            new RateLimited('strava')
        ];
    }

    private function importComment(Activity $activity, mixed $comment)
    {
        StravaComment::updateOrCreate(
            ['strava_id' => $comment['id']],
            [
                'first_name' => $comment['athlete']['firstname'],
                'last_name' => $comment['athlete']['lastname'],
                'activity_id' => $activity->id,
                'text' => $comment['text'],
                'posted_at' => Carbon::make($comment['created_at'])
            ]
        );
    }

}
