<?php

namespace App\Jobs;

use App\Events\Exchange\BroadcastCancelOrder;
use App\Events\Exchange\BroadcastPrivateCancelOrder;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelStockOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $stockOrder;
    public $stockOrderId;


    /**
     * Create a new job instance.
     *
     * @param $stockOrderId
     */
    public function __construct($stockOrderId)
    {
        $this->queue = 'cancel';
        $this->stockOrderId = $stockOrderId;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $stockOrderRepository = app(StockOrderInterface::class);

        $conditions = [
            'id' => $this->stockOrderId,
            ['status', '<', STOCK_ORDER_COMPLETED]
        ];

        $this->stockOrder = $stockOrderRepository->getFirstByConditions($conditions);

        if (empty($this->stockOrder)) {
            return false;
        }

        $broadcastOrder = true;

        if (!is_null($this->stockOrder->stop_limit) && $this->stockOrder->status == STOCK_ORDER_INACTIVE) {
            $broadcastOrder = false;
        }

        DB::beginTransaction();

        try {
            $canceledAmount = bcsub($this->stockOrder->amount, $this->stockOrder->exchanged);
            $returnBalance = bcmul($canceledAmount, $this->stockOrder->price);

            if ($this->stockOrder->exchange_type == EXCHANGE_BUY) {
                $stockItemId = $this->stockOrder->stockPair->base_item_id;
                $stockPairAttributes = [
                    'base_item_buy_order_volume' => DB::raw('base_item_buy_order_volume - ' . $returnBalance),
                    'stock_item_buy_order_volume' => DB::raw('stock_item_buy_order_volume - ' . $canceledAmount)
                ];
            } else {
                $stockItemId = $this->stockOrder->stockPair->stock_item_id;
                $stockPairAttributes = [
                    'base_item_sale_order_volume' => DB::raw('base_item_sale_order_volume -' . $returnBalance),
                    'stock_item_sale_order_volume' => DB::raw('stock_item_sale_order_volume -' . $canceledAmount)
                ];
                $returnBalance = $canceledAmount;
            }


            $attributes = [
                'canceled' => $canceledAmount,
                'status' => STOCK_ORDER_CANCELED
            ];
            $stockOrder = app(StockOrderInterface::class)->update($attributes, $this->stockOrder->id);


            if (empty($stockOrder)) {
                DB::rollBack();
                return false;
            }

            $walletAttributes = [
                'primary_balance' => DB::raw('primary_balance +' . $returnBalance),
                'on_order_balance' => DB::raw('on_order_balance -' . $returnBalance)
            ];

            $conditions = [
                'user_id' => $this->stockOrder->user_id,
                'stock_item_id' => $stockItemId
            ];
            $wallet = app(WalletInterface::class)->updateByConditions($walletAttributes, $conditions);

            if (empty($wallet)) {
                DB::rollBack();
                return false;
            }

            $stockPair = app(StockPairInterface::class)->update($stockPairAttributes, $this->stockOrder->stock_pair_id);

            if (empty($stockPair)) {
                DB::rollBack();
                return false;
            }

            $date = Carbon::now();

            $transactionAttributes = [
                [
                    'user_id' => $this->stockOrder->user_id,
                    'stock_item_id' => $stockItemId,
                    'model_name' => get_class($this->stockOrder),
                    'model_id' => $this->stockOrder->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($returnBalance, '-1'),
                    'journal' => DECREASED_FROM_ORDER_ON_ORDER_CANCELLATION,
                    'created_at' => $date,
                    'updated_at' => $date
                ],

                [
                    'user_id' => $this->stockOrder->user_id,
                    'stock_item_id' => $stockItemId,
                    'model_name' => get_class($wallet),
                    'model_id' => $wallet->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $returnBalance,
                    'journal' => INCREASED_TO_WALLET_ON_ORDER_CANCELLATION,
                    'created_at' => $date,
                    'updated_at' => $date
                ]
            ];

            $isInserted = app(TransactionInterface::class)->insert($transactionAttributes);

            if (empty($isInserted)) {
                DB::rollBack();
                return false;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }

        DB::commit();

        if (!empty($stockOrder)) {
            if($broadcastOrder){
                event(new BroadcastCancelOrder($stockOrder));
            }
            event(new BroadcastPrivateCancelOrder($stockOrder));
        }
    }
}
