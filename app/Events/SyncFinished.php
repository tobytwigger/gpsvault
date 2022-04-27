<?php

namespace App\Events;

use App\Services\Sync\Sync;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SyncFinished implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sync $sync;

    /**
     * Create a new event instance.
     *
     */
    public function __construct(Sync $sync)
    {
        $this->sync = $sync;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(sprintf('sync.%u', $this->sync->id));
    }

    public function broadcastAs()
    {
        return 'sync.finished';
    }
}
