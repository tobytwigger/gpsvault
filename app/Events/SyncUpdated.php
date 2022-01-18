<?php

namespace App\Events;

use App\Services\Sync\Sync;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SyncUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sync $sync;

    /**
     * Create a new event instance.
     *
     * @return void
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
        return new PrivateChannel(sprintf('user.%u.sync', $this->sync->user_id));
    }

    public function broadcastAs()
    {
        return 'sync.updated';
    }
}
