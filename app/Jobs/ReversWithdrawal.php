<?php

namespace App\Jobs;

use App\Exceptions\JobException;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class ReversWithdrawal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $withdrawalId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($withdrawalId)
    {
        $this->queue = 'withdrawal';
        $this->withdrawalId = $withdrawalId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $withdrawalRepository = app(WithdrawalInterface::class);

            $withdrawal = $withdrawalRepository->getFirstByConditions(['id' => $this->withdrawalId, 'status' => PAYMENT_DECLINED], ['stockItem', 'wallet']);

            if (!$withdrawal) {
                throw new JobException('No withdrawal entry found');
            }

            // returning balance to user
            $walletAttributes = ['primary_balance' => DB::raw('primary_balance + ' . $withdrawal->amount)];
            $walletConditions = [
                'id' => $withdrawal->wallet_id,
                'user_id' => $withdrawal->user_id,
                'stock_item_id' => $withdrawal->stock_item_id
            ];

            app(walletInterface::class)->updateByConditions($walletAttributes, $walletConditions);

            $date = now();
            $transactionParameters = [
                [
                    'user_id' => $withdrawal->user_id,
                    'stock_item_id' => $withdrawal->stock_item_id,
                    'model_name' => get_class($withdrawal),
                    'model_id' => $withdrawal->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($withdrawal->amount, '-1'),
                    'journal' => DECREASED_FROM_WITHDRAWAL_ON_WITHDRAWAL_CANCELLATION,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
                [
                    'user_id' => $withdrawal->user_id,
                    'stock_item_id' => $withdrawal->stock_item_id,
                    'model_name' => get_class($withdrawal->wallet),
                    'model_id' => $withdrawal->wallet->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $withdrawal->amount,
                    'journal' => INCREASED_TO_WALLET_ON_WITHDRAWAL_CANCELLATION,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
            ];
            app(TransactionInterface::class)->insert($transactionParameters);

            // notify user
            app(NotificationInterface::class)->create([
                'user_id' => $withdrawal->user_id,
                'data' => __("Your withdrawal request of :amount :stockItem to :address has been declined.", ['amount' => $withdrawal->amount, 'stockItem' => $withdrawal->stockItem->item, 'address' => $withdrawal->address])
            ]);

            return null;
        }
        catch (\Exception $exception)
        {
            logs()->error('Failed to decline withdrawal[ id='. $this->withdrawalId .'] : ' . $exception->getMessage());
        }
    }
}
