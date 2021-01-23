<?php

namespace App\Events;

use App\ScratchCard;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ScratchCardWinner
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $scratch_card;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ScratchCard $scratch_card)
    {
        $this->scratch_card = $scratch_card;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
