<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private bool $canPass;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(bool $canPass = true)
    {
        $this->canPass = $canPass;
    }

    public function alias(): ?string
    {
        return 'process-podcast';
    }

    public function tags(): array
    {
        return ['podcast' => 5];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $messages = [
            0 => 'Uploading podcast',
            5 => '50% uploaded',
            10 => 'Podcast uploaded. Processing',
            15 => 'Extracting transcript (0%)',
            20 => 'Extracting transcript (20%)',
            25 => 'Extracting transcript (40%)',
            30 => 'Extracting transcript (60%)',
            35 => 'Extracting transcript (80%)',
            40 => 'Extracting transcript (100%)',
            45 => 'Encoding (0%)',
            50 => 'Encoding (20%)',
            55 => 'Encoding (40%)',
            60 => 'Encoding (60%)',
            65 => 'Encoding (80%)',
            70 => 'Encoding (100%)',
            75 => 'Uploading to castbox',
            90 => 'Finished uploading',
            95 => 'Cleaning up',
        ];
        $this->message('Uploading podcast');
        $i = 0;
        while($i < 99) {
            usleep(50000);
            $this->percentage($i);
            if(array_key_exists($i, $messages)) {
                $this->message($messages[$i]);
            }
            $this->checkForSignals();
            $i++;
        }
        if($this->canPass === false) {
            throw new \Exception('The uploaded file was too large. Please compress it and try again.');
        }
        $this->successMessage('Your podcast has been uploaded!');
    }

    public function onCancel()
    {
        $this->message('Your podcast was not uploaded.');
    }
}
