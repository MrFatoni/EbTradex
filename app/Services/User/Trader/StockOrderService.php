<?php

namespace App\Services\User\Trader;


use App\Events\Exchange\BroadcastOrder;
use App\Events\Exchange\BroadcastPrivateOrder;
use App\Jobs\CancelStockOrder;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\DataListService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOrderService
{
    public $makerFee = 0;
    public $takerFee = 0;
    public $minimumTransactionFee = '0';
    public $transactionFeeInPercentage = 0;
    public $stockPair = null;
    public $request = null;
    public $user;
    public $stockOrder;

    public function __construct(StockOrderInterface $stockOrder)
    {
        $this->user = Auth::user();
        $this->stockOrder = $stockOrder;
    }

    public function order($request)
    {
        $user = Auth::user();

        if ($user->userInfo->is_id_verified != ID_STATUS_VERIFIED) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __("Your account must be ID verified to place an order.")
            ];
        }

        $conditions = [
            'stock_pairs.id' => $request->stock_pair_id,
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
        ];

        $stockPair = app(StockPairInterface::class)->getFirstStockPairDetailByConditions($conditions);

        if (empty($stockPair)) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('The order currently is not available to be placed.')
            ];
        }

        $this->setPropertiesValue($request, $stockPair);

        $minimumOrderTotal = get_minimum_order_total($this->minimumTransactionFee);

        if (bccomp($minimumOrderTotal, bcmul($request->amount, $request->price)) == 1) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __("Total must be :minimumOrderTotal.", ['minimumOrderTotal' => $minimumOrderTotal])
            ];
        }

        try {
            DB::beginTransaction();

            $wallet = $this->_deductBalanceFromWallet();

            if (empty($wallet)) {

                DB::rollBack();

                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __("Invalid request.")
                ];
            }

            $updateStockPair = $this->_increaseOnOrderOnStockPair();

            if (empty($updateStockPair)) {

                DB::rollBack();

                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __("Failed to place order.")
                ];
            }

            $stockOrder = $this->_placeOrder();

            if (empty($stockOrder)) {

                DB::rollBack();

                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __("Failed to place order.")
                ];
            }

            if (!$this->insertTransactionHistories($stockOrder, $wallet)) {

                DB::rollBack();

                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __("Failed to place order.")
                ];
            }


        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('You don\'t have enough balance to place order.')
            ];
        }

        DB::commit();

        event(new BroadcastOrder($stockOrder));
        event(new BroadcastPrivateOrder($stockOrder));

        return [
            SERVICE_RESPONSE_STATUS => true,
            SERVICE_RESPONSE_MESSAGE => __("Your order has been placed successfully.")
        ];
    }

    function setPropertiesValue($request, $stockPair)
    {
        $this->minimumTransactionFee = $stockPair->base_item_type == CURRENCY_REAL ? MINIMUM_TRANSACTION_FEE_CURRENCY : MINIMUM_TRANSACTION_FEE_CRYPTO;

        $adminSettings = admin_settings(['exchange_maker_fee', 'exchange_taker_fee']);
        $this->makerFee = $adminSettings['exchange_maker_fee'];
        $this->takerFee = $adminSettings['exchange_taker_fee'];

        $this->transactionFeeInPercentage = bccomp($this->makerFee, $this->takerFee) > 0 ? $this->takerFee : $this->makerFee;
        $this->stockPair = $stockPair;
        $this->request = $request;

    }

    public function _deductBalanceFromWallet()
    {
        if ($this->request->exchange_type == EXCHANGE_BUY) {
            $getRelevantWalletId = $this->stockPair->base_item_id;
            $getVolumeToBeDeducted = bcmul($this->request->price, $this->request->amount);
        } else {
            $getRelevantWalletId = $this->stockPair->stock_item_id;
            $getVolumeToBeDeducted = $this->request->amount;
        }

        // check if required balance is available and deduct the amount from wallet
        $attributes = [
            'primary_balance' => DB::raw('primary_balance - ' . $getVolumeToBeDeducted),
            'on_order_balance' => DB::raw('on_order_balance + ' . $getVolumeToBeDeducted)
        ];
        $conditions = ['user_id' => Auth::id(), 'stock_item_id' => $getRelevantWalletId];

        return app(WalletInterface::class)->updateByConditions($attributes, $conditions);
    }

    public function _increaseOnOrderOnStockPair()
    {
        if ($this->request->exchange_type == EXCHANGE_BUY) {
            $attributes['base_item_buy_order_volume'] = DB::raw('base_item_buy_order_volume + ' . bcmul($this->request->price, $this->request->amount));
            $attributes['stock_item_buy_order_volume'] = DB::raw('stock_item_buy_order_volume + ' . $this->request->amount);
        } else {
            $attributes['base_item_sale_order_volume'] = DB::raw('base_item_sale_order_volume + ' . bcmul($this->request->price, $this->request->amount));
            $attributes['stock_item_sale_order_volume'] = DB::raw('stock_item_sale_order_volume + ' . $this->request->amount);
        }

        return app(StockPairInterface::class)->update($attributes, $this->stockPair->id);
    }

    public function _placeOrder()
    {
        $orderInput = [
            'user_id' => Auth::id(),
            'stock_pair_id' => $this->stockPair->id,
            'category' => $this->request->category,
            'exchange_type' => $this->request->exchange_type,
            'price' => $this->request->price,
            'amount' => $this->request->amount,
            'stop_limit' => $this->request->get('stop_limit', null),
            'maker_fee' => $this->makerFee,
            'taker_fee' => $this->takerFee,
            'status' => $this->request->has('stop_limit') ? 0 : 1
        ];

        return $this->stockOrder->create($orderInput);


    }

    private function insertTransactionHistories($stockOrder, $wallet)
    {
        $amount = $stockOrder->exchange_type == EXCHANGE_BUY ? bcmul($stockOrder->amount, $stockOrder->price) : $stockOrder->amount;

        $date = Carbon::now();

        $inputs = [
            [
                'user_id' => $this->user->id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($wallet),
                'model_id' => $wallet->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($amount, '-1'),
                'journal' => DECREASED_FROM_WALLET_ON_ORDER_PLACE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => $this->user->id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($stockOrder),
                'model_id' => $stockOrder->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'journal' => INCREASED_TO_ORDER_ON_ORDER_PLACE,
                'created_at' => $date,
                'updated_at' => $date,
            ]
        ];

        return app(TransactionInterface::class)->insert($inputs);
    }

    /**
     * @param int $makerFee
     */
    public function setMakerFee(int $makerFee): void
    {
        $this->makerFee = $makerFee;
    }

    /**
     * @param int $takerFee
     */
    public function setTakerFee(int $takerFee): void
    {
        $this->takerFee = $takerFee;
    }

    /**
     * @param int $minimumTransactionFee
     */
    public function setMinimumTransactionFee(int $minimumTransactionFee): void
    {
        $this->minimumTransactionFee = $minimumTransactionFee;
    }

    /**
     * @param null $stockPair
     */
    public function setStockPair($stockPair): void
    {
        $this->stockPair = $stockPair;
    }

    public function cancelOrder($id)
    {
        dispatch(new CancelStockOrder($id));

        return [
            SERVICE_RESPONSE_STATUS => true,
            SERVICE_RESPONSE_MESSAGE => __('Order cancel has been placed successfully.')
        ];
    }

    public function openOrders($categoryType = null, $stockPairId = null)
    {
        $searchFields = [
            ['stock_orders.stock_pair_id', __('Market')],
            ['stock_orders.price', __('Price')],
            ['stock_orders.amount', __('Amount')],
        ];

        $orderFields = [
            ['stock_orders.price', __('Price')],
            ['stock_orders.amount', __('Amount')],
            ['stock_order.created_at', __('Date')],
        ];

        $where = [
            ['stock_orders.status', '<', STOCK_ORDER_COMPLETED]
        ];

        $where[] = ['stock_orders.user_id' => Auth::id()];

        if (!is_null($stockPairId)) {
            $where['stock_orders.stock_pair_id'] = $stockPairId;
        }

        if (!is_null($categoryType)) {
            $where['stock_orders.category'] = config('commonconfig.category_slug.' . $categoryType);
        }

        $select = [
            'stock_orders.*',
            // stock item
            'stock_items.id as stock_item_id',
            'stock_items.item as stock_item_abbr',
            'stock_items.item_name as stock_item_name',
            'stock_items.item_type as stock_item_type',
            // base item
            'base_items.id as base_item_id',
            'base_items.item as base_item_abbr',
            'base_items.item_name as base_item_name',
            'base_items.item_type as base_item_type',
            'email',
        ];
        $joinArray = [
            ['stock_pairs', 'stock_pairs.id', '=', 'stock_orders.stock_pair_id'],
            ['stock_items', 'stock_items.id', '=', 'stock_pairs.stock_item_id'],
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
            ['users', 'users.id', '=', 'stock_orders.user_id'],
        ];

        $query = app(StockOrderInterface::class)->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return app(DataListService::class)->dataList($query, $searchFields, $orderFields);
    }
}