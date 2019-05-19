<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Trader\DepositRequest;
use App\Http\Requests\User\Trader\WithdrawalRequest;
use App\Jobs\StockItemWithdrawal;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Trader\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    private $walletRepository;
    private $walletService;

    public function __construct(WalletInterface $walletRepository, WalletService $walletService)
    {
        $this->walletRepository = $walletRepository;
        $this->walletService = $walletService;
    }

    public function index()
    {
        $this->walletRepository->createUnavailableWallet(Auth::id());
        $data['list'] = $this->walletService->getWallets(Auth::id());
        $data['title'] = __('Wallets');

        return view('frontend.wallets.index', $data);
    }

    public function createDeposit($id)
    {
        $data['wallet'] = $this->walletRepository->findOrFailByConditions(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['title'] = __('Wallets');

        if ($data['wallet']->stockItem->item_type == CURRENCY_CRYPTO) {
            $data['walletAddress'] = __('Deposit is currently disabled.');

            if ($data['wallet']->stockItem->deposit_status == ACTIVE_STATUS_ACTIVE) {
                if (!empty($data['wallet']->address)) {
                    $data['walletAddress'] = $data['wallet']->address;
                } else {
                    $data['walletAddress'] = $this->walletService->generateWalletAddress($data['wallet']);
                }
            }

            return view('frontend.wallets.wallet_address', $data);
        } elseif ($data['wallet']->stockItem->item_type == CURRENCY_REAL) {
            return view('frontend.wallets.deposit_form', $data);
        } else {
            return view('errors.404', $data);
        }
    }

    public function storeDeposit(DepositRequest $request, $id)
    {
        $response = $this->walletService->storeDeposit($request, $id);

        if ($response[SERVICE_RESPONSE_STATUS] == true) {
            return redirect()->route('trader.wallets.index')->with(SERVICE_RESPONSE_SUCCESS, $response[SERVICE_RESPONSE_MESSAGE]);
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, $response[SERVICE_RESPONSE_MESSAGE]);

    }

    // paypal return url
    public function completePayment(Request $request)
    {
        $response = $this->walletService->completePayment($request);
        $return = [SERVICE_RESPONSE_ERROR => $response[SERVICE_RESPONSE_MESSAGE]];

        if ($response[SERVICE_RESPONSE_STATUS] == true) {
            $return = [SERVICE_RESPONSE_SUCCESS => $response[SERVICE_RESPONSE_MESSAGE]];
        }

        return redirect()->route('trader.wallets.index')->with($return);
    }

    // paypal cancel url
    public function cancelPayment()
    {
        $response = $this->walletService->cancelPayment();

        return redirect()->route('trader.wallets.index')->with([SERVICE_RESPONSE_WARNING => $response[SERVICE_RESPONSE_MESSAGE]]);
    }

    public function createWithdrawal($id)
    {
        $data['wallet'] = $this->walletRepository->findOrFailByConditions(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['title'] = __('Wallets');

        return view('frontend.wallets.withdrawal_form', $data);
    }

    public function storeWithdrawal(WithdrawalRequest $request, $id)
    {
        $wallet = $this->walletRepository->getFirstByConditions(['id' => $id, 'user_id' => Auth::id()], 'stockItem');

        if (empty($wallet) || !in_array($wallet->stockItem->item_type, config('commonconfig.currency_transferable'))) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid request.'));
        }

        if ($wallet->stockItem->withdrawal_status != ACTIVE_STATUS_ACTIVE) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Withdrawal is currently disabled.'));
        }

        if ( $wallet->primary_balance < $request->amount ) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('You don\'t have enough balance.'));
        }

        if ( $request->amount < $wallet->stockItem->minimum_withdrawal_amount ) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Minimum withdrawal amount :amount :stockItem', [ 'amount' => $wallet->stockItem->minimum_withdrawal_amount, 'stockItem' => $wallet->stockItem->item]));
        }

        $last24hrWithrawalAmount = app(WithdrawalInterface::class)->getLast24hrWithrawalAmount(['wallet_id' => $wallet->id, 'user_id' => Auth::id()]);

        if (bcadd($last24hrWithrawalAmount, $request->amount) > $wallet->stockItem->daily_withdrawal_limit) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Daily withdraw limit is already exceeded.'));
        }

        // started code here
        $systemFee = bcdiv(bcmul($request->amount, $wallet->stockItem->withdrawal_fee), '100');

        $attributes = ['primary_balance' => DB::raw('primary_balance - ' . $request->amount)];
        $conditions = ['id' => $wallet->id, 'user_id' => auth()->id()];

        try {
            DB::beginTransaction();

            if (!$this->walletRepository->updateByConditions($attributes, $conditions))
            {
                throw new \Exception(__('Failed to withdraw the amount. Please try again.'));
            }
            // create withdrawal
            $withdrawalRepository = app(WithdrawalInterface::class);
            $withdrawalAttriburtes = [
                'user_id' => auth()->id(),
                'ref_id' => (string)Str::uuid(),
                'wallet_id' => $wallet->id,
                'stock_item_id' => $wallet->stock_item_id,
                'amount' => $request->amount,
                'system_fee' => $systemFee,
                'address' => $request->address,
                'status' => admin_settings('auto_withdrawal_process') != ACTIVE_STATUS_ACTIVE ? PAYMENT_REVIEWING : PAYMENT_PENDING,
                'payment_method' => $wallet->StockItem->api_service,
            ];
            $withdrawal = $withdrawalRepository->create($withdrawalAttriburtes);
            //create transaction
            $date = now();
            $transactionParameters = [
                [
                    'user_id' => auth()->id(),
                    'stock_item_id' => $withdrawal->stock_item_id,
                    'model_name' => get_class($wallet),
                    'model_id' => $wallet->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($withdrawal->amount, '-1'),
                    'journal' => DECREASED_FROM_WALLET_ON_WITHDRAWAL_REQUEST,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
                [
                    'user_id' => auth()->id(),
                    'stock_item_id' => $withdrawal->stock_item_id,
                    'model_name' => get_class($withdrawal),
                    'model_id' => $withdrawal->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $withdrawal->amount,
                    'journal' => INCREASED_TO_WITHDRAWAL_ON_WITHDRAWAL_REQUEST,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
                [
                    'user_id' => auth()->id(),
                    'stock_item_id' => $withdrawal->stock_item_id,
                    'model_name' => get_class($withdrawal),
                    'model_id' => $withdrawal->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($systemFee, '-1'),
                    'journal' => DECREASED_FROM_WITHDRAWAL_AS_WITHDRAWAL_FEE_ON_WITHDRAWAL_CONFIRMATION,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
                [
                    'user_id' => auth()->id(),
                    'stock_item_id' => $withdrawal->stock_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $systemFee,
                    'journal' => INCREASED_TO_SYSTEM_ON_AS_WITHDRAWAL_FEE_WITHDRAWAL_CONFIRMATION,
                    'updated_at' => $date,
                    'created_at' => $date,
                ]
            ];
            app(TransactionInterface::class)->insert($transactionParameters);
            // notify user

            $notificationMessage = __("Your request for withdrawal :amount :stockItem to :address is now reviewing by system. You will be notified when it's transfered.", ['amount'=> $withdrawal->amount,'stockItem' => $wallet->stockItem->item,'address' => $withdrawal->address]);

            if( admin_settings('auto_withdrawal_process') == ACTIVE_STATUS_ACTIVE )
            {
                $notificationMessage = __("Your request for withdrawal :amount :stockItem to :address is now pending.", ['amount'=> $withdrawal->amount,'stockItem' => $wallet->stockItem->item,'address' => $withdrawal->address]);
            }

            app(NotificationInterface::class)->create([
                'user_id' => auth()->id(),
                'data' => $notificationMessage
            ]);

            $message = __("Your withdrawal request has been placed for reviewing. You will be notified when it's transfered.");

            if( admin_settings('auto_withdrawal_process') == ACTIVE_STATUS_ACTIVE )
            {
                $message = __("Your withdrawal request has been placed successfully.");
                dispatch(new StockItemWithdrawal($withdrawal->id));
            }

            DB::commit();

            return redirect()->route('reports.trader.withdrawals', ['id' => $wallet->id])->with(SERVICE_RESPONSE_SUCCESS, $message);
        }
        catch (\Exception $exception)
        {
            DB::rollBack();

            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to withdraw the amount. Please try again.'));
        }
    }
}