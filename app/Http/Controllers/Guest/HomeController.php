<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;


class HomeController extends Controller
{
    public function __invoke()
    {

        $conditions = ['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE];
        $postConditions = ['is_published' => ACTIVE_STATUS_ACTIVE];

        $data['title'] = __('Home');
        $data['stockPairs'] = app(StockPairInterface::class)->getAllStockPairDetailByConditions($conditions);
        $data['posts'] = app(PostInterface::class)->getLatestByCondition($postConditions,3, ['comments']);

        return view('home', $data);
    }
}
