<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastStockExchange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payloadData;
    public $stockPairId;

    /**
     * Create a new event instance.
     *
     * @param $stockPairId
     * @param $payloadData
     */
    public function __construct($stockPairId, $payloadData)
    {
        $this->stockPairId = $stockPairId;
        $this->payloadData = $payloadData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel(channel_prefix() .'exchange.' . $this->stockPairId);
    }

    public function broadcastWith()
    {
        return $this->payloadData;
    }
}
