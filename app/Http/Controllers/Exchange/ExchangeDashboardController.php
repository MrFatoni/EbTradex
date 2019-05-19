<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Services\Backend\ExchangeDashboardService;
use App\Services\Exchange\StockGraphDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ExchangeDashboardController extends Controller
{
    public $exchangeService;

    public function __construct(ExchangeDashboardService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function index($pair = null)
    {
        $data['title'] = __('Exchange');
        $data['stockPair'] = $this->exchangeService->getDefaultStockPair($pair);

        abort_if(empty($data['stockPair']), 404, __('Exchange not found for this pair.'));

        $data['categoryID'] = CATEGORY_EXCHANGE;

        $data['chartInterval'] = Cookie::get('chartInterval');
        if (empty($data['chartInterval'])) {
            $data['chartInterval'] = 240;
            cookie()->forever('chartInterval', $data['chartInterval']);
        }

        $data['chartZoom'] = Cookie::get('chartZoom');
        if (empty($data['chartZoom'])) {
            $data['chartZoom'] = 20160;
            cookie()->forever('chartZoom', $data['chartZoom']);
        }

        return view('frontend.exchange.index', $data);
    }

    public function get24HrPairDetail($stockPairID)
    {
        $response = $this->exchangeService->get24HrPairDetail($stockPairID);

        return response()->json($response);
    }

    public function getStockMarket()
    {
        $stockMarkets = $this->exchangeService->getStockMarket();
        $baseItems = [];
        foreach ($stockMarkets as $stockMarket) {
            $baseItems[$stockMarket->base_item_id] = $stockMarket->base_item_abbr;
        }

        $response = [
            'stockItems' => $stockMarkets->toArray(),
            'baseItems' => $baseItems,
        ];
        return response()->json($response);
    }

    public function getOrders(Request $request)
    {
        $response = $this->exchangeService->getOrders($request->stock_pair_id, $request->last_price, $request->exchange_type, $request->exchange_category);


        return response()->json($response);
    }

    public function getChartData(Request $request)
    {
        $chartData = app(StockGraphDataService::class)->getGraphData($request->stock_pair_id, $request->interval);
//        $chartData = json_decode(file_get_contents(asset('dummy-chart-data.json')), true);
        return response()->json($chartData)->cookie('stockPairID', $request->stock_pair_id)->cookie('chartInterval', $request->interval);
    }

    public function getMyOpenOrders(Request $request)
    {
        $conditions = [
            'user_id' => Auth::id(),
            'stock_pair_id' => $request->stock_pair_id,
            ['status', '<', STOCK_ORDER_COMPLETED]
        ];
        $myOpenOrders = app(StockOrderInterface::class)->getMyOpenOrders($conditions);
        return response()->json($myOpenOrders);
    }

    public function getTradeHistories(Request $request)
    {
        $conditions = [
            'stock_exchanges.stock_pair_id' => $request->stock_pair_id,
            'stock_orders.category' => CATEGORY_EXCHANGE,
            'stock_exchanges.is_maker' => 1
        ];

        $tradeHistories = app(StockExchangeInterface::class)->getLatest($conditions, TRADE_HISTORY_PER_PAGE);
        return response()->json($tradeHistories);
    }

    public function getMyTrade(Request $request)
    {
        $conditions = [
            'stock_exchanges.stock_pair_id' => $request->stock_pair_id,
            'stock_orders.category' => CATEGORY_EXCHANGE,
            'stock_exchanges.user_id' => Auth::id()
        ];

        $tradeHistories = app(StockExchangeInterface::class)->getLatest($conditions, TRADE_HISTORY_PER_PAGE);
        return response()->json($tradeHistories);
    }

    public function getWalletSummary(Request $request)
    {
        $walletSummary = $this->exchangeService->getWalletSummary($request->stock_pair_id);
        return response()->json($walletSummary);
    }


}
