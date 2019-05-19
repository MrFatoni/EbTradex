<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastPrivateCancelOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $stockOrder;

    /**
     * Create a new event instance.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->stockOrder = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(channel_prefix() .'orders.' . $this->stockOrder->stock_pair_id . '.' . $this->stockOrder->user_id);
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return $this->stockOrder->category == CATEGORY_EXCHANGE;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'order_number' => $this->stockOrder->id,
            'exchange_type' => $this->stockOrder->exchange_type,
            'price' => $this->stockOrder->price,
            'amount' => $this->stockOrder->canceled,
            'total' => bcmul($this->stockOrder->canceled, $this->stockOrder->price)
        ];
    }
}
