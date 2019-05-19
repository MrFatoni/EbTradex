<?php

namespace App\Events\Exchange;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastPrivateOrder implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $order;

    /**
     * Create a new event instance.
     *
     * @param $order
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(channel_prefix() .'orders.' . $this->order->stock_pair_id . '.' . $this->order->user_id);
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return $this->order->category == CATEGORY_EXCHANGE;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'order_number' => $this->order->id,
            'exchange_type' => $this->order->exchange_type,
            'price' => $this->order->price,
            'amount' => $this->order->amount,
            'exchanged' => $this->order->exchanged,
            'total' => bcmul($this->order->amount, $this->order->price),
            'stop_limit' => $this->order->stop_limit,
            'date' => $this->order->created_at->toDateTimeString()
        ];
    }
}
