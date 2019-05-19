<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastSettlementOrders implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stockPairId;
    public $orders;

    /**
     * Create a new event instance.
     *
     * @param $stockPairId
     * @param $orders
     */
    public function __construct($stockPairId, $orders)
    {
        $this->stockPairId = $stockPairId;
        $this->orders = $orders;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel(channel_prefix() .'exchange.'.$this->stockPairId);
    }

    public function broadcastWith()
    {
        return $this->orders;
    }
}
