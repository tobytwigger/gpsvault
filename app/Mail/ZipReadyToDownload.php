<?php

namespace App\Mail;

use App\Models\File;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ZipReadyToDownload extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public File $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, File $file)
    {
        $this->user = $user;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.data-ready');
    }
}
