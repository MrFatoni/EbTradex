<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastStockPairSummary implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $payloadData;

    /**
     * Create a new event instance.
     *
     * @param $payloadData
     */
    public function __construct($payloadData)
    {
        $this->payloadData = $payloadData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel(channel_prefix() .'exchange');
    }

    public function broadcastWith()
    {
        return $this->payloadData;
    }
}
