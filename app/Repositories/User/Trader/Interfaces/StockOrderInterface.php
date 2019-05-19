<?php

namespace App\Repositories\User\Trader\Interfaces;

interface StockOrderInterface
{
    public function getStopLimitOrders($conditions, $stockPrice);

    public function getStopLimitOrdersByIds($ids);
}