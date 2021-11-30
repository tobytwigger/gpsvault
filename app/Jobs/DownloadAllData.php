<?php

namespace App\Jobs;

use App\Mail\ZipReadyToDownload;
use App\Models\Activity;
use App\Models\ConnectionLog;
use App\Models\Sync;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DownloadAllData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $zipCreator = ZipCreator::start($this->user);
        $zipCreator->add($this->user);
        foreach(Activity::where('user_id', $this->user->id)->get() as $activity) {
            $zipCreator->add($activity);
        }
        foreach(Sync::where('user_id', $this->user->id)->get() as $sync) {
            $zipCreator->add($sync);
        }
        foreach(ConnectionLog::where('user_id', $this->user->id)->get() as $connectionLog) {
            $zipCreator->add($connectionLog);
        }
        $file = $zipCreator->archive();

        Mail::to($this->user->email)
            ->send(new ZipReadyToDownload($this->user, $file));
    }
}
