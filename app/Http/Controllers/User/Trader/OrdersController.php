<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\StockOrderRequest;
use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Trader\StockOrderService;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    private $stockOrderService;
    private $depositRepository;
    private $withdrawalRepository;

    public function __construct(DepositInterface $deposit, WithdrawalInterface $withdrawal, StockOrderService $stockOrderService)
    {
        $this->depositRepository = $deposit;
        $this->withdrawalRepository = $withdrawal;
        $this->stockOrderService = $stockOrderService;
    }

    public function openOrders()
    {
        $data['list'] = $this->stockOrderService->openOrders();
        $data['title'] = __('Open Orders');

        return view('frontend.orders.open_orders', $data);
    }

    public function store(StockOrderRequest $request)
    {
        $response = app(StockOrderService::class)->order($request);

        if ($response[SERVICE_RESPONSE_STATUS]) {
            return response()->json([SERVICE_RESPONSE_SUCCESS => $response[SERVICE_RESPONSE_MESSAGE]]);
        }

        return response()->json([SERVICE_RESPONSE_ERROR => $response[SERVICE_RESPONSE_MESSAGE]]);

    }

    public function destroy(StockOrderInterface $stockOrderRepository, $id)
    {
        $stockOrder = $stockOrderRepository->getFirstById($id);

        if (empty($stockOrder)) {
            return response()->json([SERVICE_RESPONSE_ERROR => __('Order not found.')]);
        }


        if (Auth::id() != $stockOrder->user_id) {
            return response()->json([SERVICE_RESPONSE_ERROR => __('You are not authorize to do this action.')]);
        }

        if ($stockOrder->status >= STOCK_ORDER_COMPLETED) {
            return response()->json([SERVICE_RESPONSE_ERROR => __('This order cannot be deleted.')]);
        }


        $response = app(StockOrderService::class)->cancelOrder($id);
        if ($response[SERVICE_RESPONSE_STATUS]) {
            return response()->json([SERVICE_RESPONSE_SUCCESS => $response[SERVICE_RESPONSE_MESSAGE]]);
        }

        return response()->json([SERVICE_RESPONSE_ERROR => $response[SERVICE_RESPONSE_MESSAGE]]);
    }
}