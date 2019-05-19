<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastPrivateStockExchange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stockPairId;
    public $userId;
    public $stockExchange;

    /**
     * Create a new event instance..' . $this->userId
     *
     * @param $stockPairId
     * @param $userId
     * @param $stockExchange
     */
    public function __construct($stockPairId, $userId, $stockExchange)
    {
        $this->stockPairId = $stockPairId;
        $this->userId = $userId;
        $this->stockExchange = $stockExchange;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(channel_prefix() .'exchange.' . $this->stockPairId . '.' . $this->userId);
    }


    public function broadcastWith()
    {
        return $this->stockExchange;
    }
}
