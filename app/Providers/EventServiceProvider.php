<?php

namespace App\Providers;

use App\Events\Exchange\BroadcastCancelOrder;
use App\Events\Exchange\BroadcastOrder;
use App\Events\Exchange\BroadcastPrivateCancelOrder;
use App\Events\Exchange\BroadcastPrivateOrder;
use App\Listeners\Exchange\ProcessStockOrderInQueue;
use App\Listeners\Exchange\ProcessStopLimitStockOrderInQueue;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BroadcastOrder::class => [
            ProcessStockOrderInQueue::class,
            ProcessStopLimitStockOrderInQueue::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
