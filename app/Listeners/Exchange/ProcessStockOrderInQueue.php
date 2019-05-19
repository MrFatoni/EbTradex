<?php

namespace App\Listeners\Exchange;

use App\Events\Exchange\BroadcastOrder;
use App\Services\Exchange\StockExchangeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessStockOrderInQueue implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'exchange';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param BroadcastOrder $event
     * @return void
     */
    public function handle(BroadcastOrder $event)
    {
        app(StockExchangeService::class, [$event->order])->process();
    }

    public function shouldQueue($event)
    {
        return (
            $event->order->status == STOCK_ORDER_PENDING &&
            $event->order->category == CATEGORY_EXCHANGE &&
            is_null($event->order->stop_limit)
        );
    }
}
