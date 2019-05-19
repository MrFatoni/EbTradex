<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Requests\User\Trader\IcoStoreRequest;
use App\Models\User\Wallet;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Http\Controllers\Controller;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IcoController extends Controller
{
    public function index()
    {
        $data['title'] = __('Exchange');
        $searchFields = [];
        $orderFields = [];
        $where = [
            'stock_items.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_items.is_ico' => ACTIVE_STATUS_ACTIVE
        ];
        $select = ['stock_items.*', 'stock_pairs.id as stock_pair_id', 'last_price', 'ico_total_sold', 'ico_total_earned', 'base_items.item_name as base_item_name', 'base_items.item as base_item'];
        $joinArray = [
            ['stock_pairs', 'stock_pairs.stock_item_id', '=', 'stock_items.id'],
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
        ];
        $query = app(StockItemInterface::class)->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);

        return view('frontend.ico.index', $data);
    }

    public function buy($id)
    {
        $data['stockPair'] = app(StockPairInterface::class)->findOrFailByConditions(['id' => $id], ['stockItem', 'baseItem']);
        $data['wallets'] = [];

        if (Auth::check()) {
            $wallets = app(WalletInterface::class)->getByConditions(['user_id' => Auth::id()]);

            foreach($wallets as $wallet) {
                $data['wallets'][$wallet->stock_item_id] = $wallet->primary_balance;
            }
        }

        $data['title'] = __('Trade ICO');
        return view('frontend.ico.buy', $data);
    }

    public function store(IcoStoreRequest $request)
    {
        $user = Auth::user();

        if ($user->userInfo->is_id_verified != ID_STATUS_VERIFIED) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __("Your account must be ID verified to make any order."));
        }

        $conditions = [
            'stock_pairs.id' => $request->stock_pair_id,
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
        ];
        $stockPair = app(StockPairInterface::class)->getFirstStockPairDetailByConditions($conditions);

        if (empty($stockPair)) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __("Invalid request."));
        }

        $icoFee = admin_settings('ico_fee');
        $totalAmount = bcmul($request->amount, $stockPair->last_price );
        $feeAmount = bcdiv( bcmul($totalAmount, $icoFee ), '100');
        $totalAmountToCharge = bcadd($totalAmount, $feeAmount);

        try {
            DB::beginTransaction();

            $updatableAttributes = [
                [
                    'conditions' => ['user_id' => Auth::id(), 'stock_item_id' => $stockPair->stock_item_id],
                    'fields' => [
                        'primary_balance' => ['increment', $request->amount]
                    ]
                ],
                [
                    'conditions' => ['user_id' => Auth::id(), 'stock_item_id' => $stockPair->base_item_id],
                    'fields' => [
                        'primary_balance' => ['decrement', $totalAmountToCharge]
                    ]
                ]
            ];
            if( app(WalletInterface::class)->bulkUpdate($updatableAttributes) < 2 ) {
                DB::rollBack();
                return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __("Failed to buy."));
            }

            // place stock order
            $orderAttributes = [
                'user_id' => Auth::id(),
                'stock_pair_id' => $stockPair->id,
                'category' => CATEGORY_ICO,
                'exchange_type' => EXCHANGE_BUY,
                'price' => $stockPair->last_price,
                'amount' => $request->amount,
                'maker_fee' => $icoFee,
                'status' => STOCK_ORDER_COMPLETED
            ];
            $buyStockOrder = app(StockOrderInterface::class)->create($orderAttributes);

            // place stock exchange
            $exchangeAttributes = [
                'user_id' => Auth::id(),
                'stock_order_id' => $buyStockOrder->id,
                'stock_pair_id' => $buyStockOrder->stock_pair_id,
                'amount' => $request->amount,
                'price' => $stockPair->last_price,
                'total' => $totalAmountToCharge,
                'fee' => $feeAmount,
                'exchange_type' => $buyStockOrder->exchange_type,
                'is_maker' => 1
            ];
            $stockExchange = app(StockExchangeInterface::class)->create($exchangeAttributes);

            // place transaction
            $date = now();
            $transactionAttributes = [
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => get_class(new Wallet()),
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($totalAmountToCharge, '-1'),
                    'journal' => DECREASED_FROM_WALLET_ON_ORDER_PLACE,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => get_class($buyStockOrder),
                    'model_id' => $buyStockOrder->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $totalAmountToCharge,
                    'journal' => INCREASED_TO_ORDER_ON_ORDER_PLACE,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => get_class($buyStockOrder),
                    'model_id' => $buyStockOrder->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($totalAmountToCharge, '-1'),
                    'journal' => DECREASED_FROM_ORDER_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $totalAmountToCharge,
                    'journal' => INCREASED_TO_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($totalAmount, '-1'),
                    'journal' => DECREASED_FROM_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $totalAmount,
                    'journal' => INCREASED_TO_SYSTEM_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($feeAmount, '-1'),
                    'journal' => DECREASED_FROM_EXCHANGE_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->base_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $feeAmount,
                    'journal' => INCREASED_TO_SYSTEM_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->stock_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($request->amount, '-1'),
                    'journal' => DECREASED_FROM_SYSTEM_ON_ICO_SALE,
                    'created_at' => $date,
                    'updated_at' => $date
                ],
                [
                    'user_id' => Auth::id(),
                    'stock_item_id' => $stockPair->stock_item_id,
                    'model_name' => get_class(new Wallet()),
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $request->amount,
                    'journal' => INCREASED_TO_WALLET_FROM_SYSTEM_ON_ICO_PURCHASE,
                    'created_at' => $date,
                    'updated_at' => $date
                ]
            ];
            app(TransactionInterface::class)->insert($transactionAttributes);

            // update stock pair total ico earn
            $stockPairAttributes = [
                'ico_total_sold' => DB::raw('ico_total_sold + ' . $request->amount),
                'ico_total_earned' => DB::raw('ico_total_earned + ' . $totalAmountToCharge),
                'ico_fee_earned' => DB::raw('ico_fee_earned + ' . $feeAmount)
            ];

            if(!app(StockPairInterface::class)->updateByConditions($stockPairAttributes, ['id' => $stockPair->id]))
            {
                DB::rollBack();
                return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __("Failed to buy."));
            }

            DB::commit();

            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __("Your buy request is completed."));
        }
        catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __("You don't have enough balance."));
        }
    }
}