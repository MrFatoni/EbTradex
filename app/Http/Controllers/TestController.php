<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\IpnController;
use App\Jobs\StopLimitStockOrder;
use App\Models\User\StockOrder;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\Api\BitcoinApi;

class TestController extends Controller
{
    /**
     * @developer: M.G. Rabbi
     * @date: 2019-01-27 5:14 PM
     * @description:
     */
    public function test()
    {
        $this->dispatch(new StopLimitStockOrder(1,1.20000000));
    }
}