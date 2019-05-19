<?php

namespace App\Services\User\Trader;

use App\Exceptions\JobException;
use App\Http\Requests\User\Trader\DepositRequest;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\Api\PaypalRestApi;
use App\Services\Core\DataListService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletService
{
    public $walletRepository;
    private $apiPath = 'App\\Services\\Api\\';

    public function __construct(WalletInterface $wallet)
    {
        $this->walletRepository = $wallet;
    }

    public function getWallets($userId)
    {
        $searchFields = [
            ['stock_items.item_name', __('Wallet Name')],
        ];

        $orderFields = [
            ['stock_items.item_name', __('Wallet Name')],
        ];

        $whereArray = ['user_id' => $userId];
        $select = ['wallets.*', 'item', 'item_name', 'item_type', 'deposit_status', 'withdrawal_status'];
        $joinArray = ['stock_items', 'stock_items.id', '=', 'wallets.stock_item_id'];

        $query = $this->walletRepository->paginateWithFilters($searchFields, $orderFields, $whereArray, $select, $joinArray);
        return app(DataListService::class)->dataList($query, $searchFields, $orderFields);
    }

    public function generateWalletAddress($wallet)
    {
        $stockApiService = null;

        if ( in_array($wallet->stockItem->api_service, array_keys(api_classes())) )
        {
            $className = $this->apiPath . api_classes($wallet->stockItem->api_service);
            $stockApiService = new $className($wallet->stockItem->item);
        }

        if (!is_null($stockApiService)) {
            $stockApiServiceResponse = $stockApiService->generateAddress();

            if (!empty($stockApiServiceResponse) && $stockApiServiceResponse['error'] == 'ok') {
                $address = $stockApiServiceResponse['result']['address'];

                if ($this->walletRepository->update(['address' => $address], $wallet->id)) {
                    return $address;
                }
            }
        }

        return __('Failed to create wallet address. Try Again.');
    }

    public function storeDeposit(DepositRequest $request, $id)
    {
        $wallet = $this->walletRepository->getFirstByConditions(['id' => $id, 'user_id' => Auth::id()], 'stockItem');

        if (empty($wallet) || $wallet->stockItem->item_type != CURRENCY_REAL) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('Invalid request.')
            ];
        }

        DB::beginTransaction();

        $depositParameters = [
            'ref_id' => (string)Str::uuid(),
            'user_id' => $wallet->user_id,
            'wallet_id' => $wallet->id,
            'stock_item_id' => $wallet->stock_item_id,
            'amount' => $request->amount,
            'payment_method' => $wallet->stockItem->api_service
        ];

        if (!$deposited = app(DepositInterface::class)->create($depositParameters)) {
            DB::rollBack();

            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('Failed to deposit.'),
            ];
        }

        if ($wallet->stockItem->api_service == API_PAYPAL) {
            $paymentService = app(PaypalRestApi::class);
            $relatedTransaction = [
                'intent' => 'sale',
                'return_url' => route('frontend.wallets.deposit.paypal.return-url'),
                'cancel_url' => route('frontend.wallets.deposit.paypal.cancel-url'),
            ];
            $paymentResponse = $paymentService->payment($request->amount, $wallet->stockItem->item, $relatedTransaction);

            if (!$paymentResponse) {
                DB::rollBack();

                return [
                    SERVICE_RESPONSE_STATUS => false,
                    SERVICE_RESPONSE_MESSAGE => __('Deposit is currently not available.')
                ];
            }

            DB::commit();
            session()->put(
                'PaypalPayment',
                [
                    'wallet_id' => $wallet->id,
                    'deposit_id' => $deposited->id,
                    'stock_item_id' => $wallet->stock_item_id,
                    'payment_id' => $paymentResponse['payment_id']
                ]
            );

            return redirect()->away($paymentResponse['return_url'])->send();
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Deposit service is not available.'),
        ];
    }

    public function completePayment(Request $request)
    {
        $paymentInfo = session()->get('PaypalPayment');

        if (empty($paymentInfo) || empty($request->get('PayerID')) || empty($request->get('token'))) {
            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('Invalid payment request')
            ];
        }

        session()->forget('PaypalPayment');

        $paymentStatus = app(PaypalRestApi::class)->getPaymentStatus($paymentInfo['payment_id'], $request->get('PayerID'));

        $transactionInfo = [
            'paid_amount' => $paymentStatus->transactions[0]->amount->total,
            'paid_currency' => $paymentStatus->transactions[0]->amount->currency,
            'paid_network_fee' => 0,
            'wallet_id' => $paymentInfo['wallet_id'],
            'deposit_id' => $paymentInfo['deposit_id'],
            'stock_item_id' => $paymentInfo['stock_item_id']
        ];

        $depositInfo = [
            'address' => $paymentStatus->payer->payer_info->email,
            'txn_id' => $paymentStatus->id,
            'status' => PAYMENT_COMPLETED,
        ];

        if ($paymentStatus->getState() == 'approved') {
            DB::beginTransaction();

            if ($this->_completePayment($transactionInfo, $depositInfo)) {
                DB::commit();

                return [
                    SERVICE_RESPONSE_STATUS => true,
                    SERVICE_RESPONSE_MESSAGE => __('Deposit payment is successful.')
                ];
            }

            DB::rollBack();

            return [
                SERVICE_RESPONSE_STATUS => false,
                SERVICE_RESPONSE_MESSAGE => __('Failed to process payment.')
            ];
        }

        return $this->_cancelPayment($paymentInfo);
    }

    public function _completePayment($transactionInfo, $depositInfo)
    {
        $stockItemRepository = app(StockItemInterface::class);
        if (!$stockItem = $stockItemRepository->getFirstById($transactionInfo['stock_item_id'])) {
            return false;
        }

        $getActualPaidAmount = bcsub($transactionInfo['paid_amount'], $transactionInfo['paid_network_fee']);
        $depositSystemFee = bcdiv(bcmul($getActualPaidAmount, $stockItem->deposit_fee), "100");
        $amount = bcsub($getActualPaidAmount, $depositSystemFee);

        $conditions = ['id' => $transactionInfo['deposit_id'], 'status' => PAYMENT_PENDING];
        $depositInfo['system_fee'] = $depositSystemFee;
        $depositInfo['network_fee'] = $transactionInfo['paid_network_fee'];

        if (!$deposited = app(DepositInterface::class)->updateByConditions($depositInfo, $conditions)) {
            return false;
        }

        $attributes = ['primary_balance' => DB::raw('primary_balance + ' . $amount)];
        $conditions = ['id' => $transactionInfo['wallet_id'], 'user_id' => Auth::id()];

        if (!$wallet = $this->walletRepository->updateByConditions($attributes, $conditions)) {
            return false;
        }

        $date = now();
        $transactionParameters = [
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($amount, '-1'),
                'journal' => DECREASED_FROM_OUTSIDE_ON_DEPOSIT_REQUEST,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($deposited),
                'model_id' => $deposited->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'journal' => INCREASED_TO_DEPOSIT_ON_DEPOSIT_REQUEST,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($deposited),
                'model_id' => $deposited->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($amount, '-1'),
                'journal' => DECREASED_FROM_DEPOSIT_ON_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($wallet),
                'model_id' => $wallet->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'journal' => INCREASED_TO_WALLET_ON_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $deposited->user_id,
                'stock_item_id' => $deposited->stock_item_id,
                'model_name' => get_class($deposited),
                'model_id' => $deposited->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($depositSystemFee, '-1'),
                'journal' => DECREASED_FROM_DEPOSIT_AS_DEPOSIT_FEE_ON_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $deposited->user_id,
                'stock_item_id' => $deposited->stock_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $depositSystemFee,
                'journal' => INCREASED_TO_SYSTEM_AS_DEPOSIT_FEE_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
        ];

        if (!app(TransactionInterface::class)->insert($transactionParameters)) {
            return false;
        }

        $notification = app(NotificationInterface::class)->create([
            'user_id' => Auth::id(),
            'data' => __("You've just deposited :amount :currency",['amount'=> $amount,'currency' => $transactionInfo['paid_currency']])
        ]);

        if (!$notification) {
            return false;
        }

        $stockItemAttributes = [
            'total_deposit' => DB::raw('total_deposit + ' . $amount),
            'total_deposit_fee' => DB::raw('total_deposit_fee + ' . $depositSystemFee)
        ];

        if (!$stockItemRepository->update($stockItemAttributes, $stockItem->id)) {
            return false;
        }

        return true;
    }

    public function _cancelPayment($paymentInfo)
    {
        app(DepositInterface::class)->updateByConditions(['status' => PAYMENT_FAILED], [
            'id' => $paymentInfo['deposit_id'], 'status' => PAYMENT_PENDING]);

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Deposit payment is canceled.')
        ];
    }

    public function cancelPayment()
    {
        $paymentInfo = session()->get('PaypalPayment');
        session()->forget('PaypalPayment');

        return $this->_cancelPayment($paymentInfo);
    }

    public function send($withdrawalId)
    {
        $withdrawalRepository = app(WithdrawalInterface::class);
        $withdrawal = $withdrawalRepository->getFirstByConditions(['id' => $withdrawalId, 'status' => PAYMENT_PENDING], ['stockItem', 'wallet']);

        try {

            if( !$withdrawal )
            {
                throw new JobException('No withdrawal entry found');
            }

            $actualAmount = bcsub($withdrawal->amount, $withdrawal->system_fee);

            $txnId = null;
            $apiService = null;

            if ( !in_array($withdrawal->stockItem->api_service, array_keys(api_classes())) )
            {
                throw new JobException('Invaild api service.');
            }

            $className = $this->apiPath . api_classes($withdrawal->stockItem->api_service);

            if ($withdrawal->stockItem->item_type == CURRENCY_CRYPTO)
            {
                $apiService = new $className($withdrawal->stockItem->item);
            }
            elseif ($withdrawal->stockItem->item_type == CURRENCY_REAL)
            {
                $apiService = new $className;
            }

            if ( is_null($apiService) )
            {
                throw new JobException('API service is not availabe for withdrawal.');
            }

            $receiverWallet = false;

            if ($withdrawal->stockItem->item_type == CURRENCY_CRYPTO)
            {
                $receiverWallet = $this->walletRepository->getFirstByConditions(['address' => $withdrawal->address], 'stockItem');

                if( !empty($receiverWallet) )
                {
                    $txnId = md5( time() );
                    $ipnResponse = [
                        'error' => 'ok',
                        'result' => [
                            'txn_status' => 'completed',
                            'payment_method' => $withdrawal->payment_method,
                            'ipn_type' => 'deposit',
                            'address' => $withdrawal->address,
                            'txn_id' => $txnId,
                            'id' => $txnId,
                            'currency' => $withdrawal->stockItem->item,
                            'amount' => $actualAmount,
                            'fee' => 0,
                        ]
                    ];

                    if ( !$this->updateTransaction( $ipnResponse ) )
                    {
                        throw new JobException('Failed to update receiver wallet.');
                    }
                }
                else
                {
                    $apiResponse = $apiService->sendToAddress($withdrawal->address, $actualAmount, $withdrawal->stockItem->item);

                    if ($apiResponse['error'] != 'ok')
                    {
                        throw new JobException($apiResponse['error']);
                    }

                    $txnId = $apiResponse['result']['txn_id'];
                }
            }
            elseif ($withdrawal->stockItem->item_type == CURRENCY_REAL)
            {
                $apiResponse = $apiService->payout($withdrawal->address, $actualAmount, $withdrawal->stockItem->item);

                if ($apiResponse['error'] != 'ok')
                {
                    throw new JobException($apiResponse['error']);
                }

                $txnId = $apiResponse['result']['txn_id'];
            }

            if (is_null($txnId))
            {
                throw new JobException('Api response not found.');
            }
        }
        catch (\Exception $exception)
        {
            logs()->error('Failed withdraw[ id='. $withdrawalId .'] : ' . $exception->getMessage());

            $this->reverseWithdraw($withdrawalId, $withdrawal->stockItem->item);

            return null;
        }

        if ( !is_null($txnId) )
        {
            // update withdrawal with txnId
            $attributes = ['txn_id' => $txnId];

            // TODO:: only for paypal and should be removed after adding paypal webhook
            if( !empty($receiverWallet) || $withdrawal->stockItem->api_service == API_PAYPAL )
            {
                $attributes['status'] = PAYMENT_COMPLETED;

                $this->withdraw($withdrawal);
            }

            // update withdrawal with txnid.
            $withdrawalRepository->update($attributes, $withdrawalId);
        }
    }

    public function updateTransaction($ipnResponse)
    {
        if ($ipnResponse['result']['txn_status'] == 'completed') {
            $txnStatus = PAYMENT_COMPLETED;
        } elseif ($ipnResponse['result']['txn_status'] == 'failed') {
            $txnStatus = PAYMENT_FAILED;
        } else {
            $txnStatus = PAYMENT_PENDING;
        }

        if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
        {
            logs()->info('log: Transaction Status: ' . $txnStatus);
        }
        // check ipn type
        if ($ipnResponse['result']['ipn_type'] == 'deposit') {
            try {
                DB::beginTransaction();

                if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
                {
                    logs()->info('log: Deposit Process Started.');
                }

                $walletRepository = app(WalletInterface::class);
                $wallet = $walletRepository->getFirstByConditions(['address' => $ipnResponse['result']['address']], 'stockItem');

                if (empty($wallet)) {
                    throw new JobException(__('Relevant wallet not found.'));
                }

                $depositParameters = [
                    'ref_id' => (string)Str::uuid(),
                    'user_id' => $wallet->user_id,
                    'wallet_id' => $wallet->id,
                    'stock_item_id' => $wallet->stock_item_id,
                    'amount' => $ipnResponse['result']['amount'],
                    'address' => $ipnResponse['result']['address'],
                    'txn_id' => $ipnResponse['result']['txn_id'],
                    'network_fee' => $ipnResponse['result']['fee'],
                    'payment_method' => $wallet->stockItem->api_service,
                    'status' => PAYMENT_PENDING,
                ];
                $conditions = [
                    'user_id' => $wallet->user_id,
                    'wallet_id' => $wallet->id,
                    'stock_item_id' => $wallet->stock_item_id,
                    'txn_id' => $ipnResponse['result']['txn_id'],
                ];
                $depositRepository = app(DepositInterface::class);

                $deposited = $depositRepository->getFirstByConditions($conditions);

                if( empty($deposited) )
                {
                    if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
                    {
                        logs()->info(' log: No Previous Deposit found.');
                    }

                    $deposited = $depositRepository->create($depositParameters);
                }

                if ( empty($deposited) )
                {
                    if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
                    {
                        logs()->info(' log: Failed to create deposit');
                    }

                    throw new JobException(__('Failed to record deposit.'));
                }

                if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
                {
                    logs()->info('log: deposit created / found with pending status.');
                }

                if ( $txnStatus == PAYMENT_COMPLETED && $deposited->status == PAYMENT_PENDING )
                {
                    $depositResponse = $this->deposit($ipnResponse, $wallet, $deposited);

                    if ( $depositResponse[SERVICE_RESPONSE_STATUS] == SERVICE_RESPONSE_ERROR )
                    {
                        throw new JobException( $depositResponse[SERVICE_RESPONSE_MESSAGE] );
                    }
                }
                elseif ($txnStatus == PAYMENT_FAILED && $deposited->status == PAYMENT_PENDING)
                {
                    if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
                    {
                        logs()->info('log: IPN payment is failed and deposit status is found as pending.');
                    }

                    if (!$depositRepository->updateByConditions(['status' => PAYMENT_FAILED], ['id' => $deposited->id])) {
                        throw new JobException(__('Failed to update deposit status.'));
                    }
                }

                DB::commit();

                return true;

            } catch (\Exception $exception) {
                DB::rollBack();
                logs()->error($exception->getMessage());

                return false;
            }
        }
        elseif ($ipnResponse['result']['ipn_type'] == 'withdrawal')
        {
            try {
                if ($txnStatus == PAYMENT_COMPLETED)
                {
                    DB::beginTransaction();

                    $attributes = ['txn_id' => $ipnResponse['result']['txn_id'], 'status' => PAYMENT_COMPLETED];
                    $conditions = ['txn_id' => $ipnResponse['result']['id'], 'status' => PAYMENT_PENDING];
                    $withdrawal = app(WithdrawalInterface::class)->updateByConditions($attributes, $conditions);

                    if (!$withdrawal)
                    {
                        throw new JobException(__("Withdrawal ID :id does not exists or the transaction is already processed as completed.", ['id' => $ipnResponse['result']['id']]));
                    }

                    // make transaction
                    $withdrawalResponse = $this->withdraw($withdrawal);

                    if ( $withdrawalResponse[SERVICE_RESPONSE_STATUS] != SERVICE_RESPONSE_SUCCESS )
                    {
                        throw new JobException(__('Failed to update withdrawal status.'));
                    }

                    DB::commit();

                    return true;
                }
                elseif ($txnStatus == PAYMENT_FAILED)
                {
                    DB::beginTransaction();

                    $attributes = ['txn_id' => $ipnResponse['result']['txn_id'], 'status' => PAYMENT_FAILED];
                    $conditions = ['txn_id' => $ipnResponse['result']['id'], 'status' => PAYMENT_PENDING];
                    $withdrawal = app(WithdrawalInterface::class)->updateByConditions($attributes, $conditions);

                    if (!$withdrawal) {
                        throw new JobException(__("Withdrawal ID :id does not exists or the transaction is already processed as failed.", ['id' => $ipnResponse['result']['id']]));
                    }

                    // returning balance to user
                    $walletAttributes = ['primary_balance' => DB::raw('primary_balance + ' . $withdrawal->amount)];
                    $walletConditions = [
                        'id' => $withdrawal->wallet_id,
                        'user_id' => $withdrawal->user_id,
                        'stock_item_id' => $withdrawal->stock_item_id
                    ];

                    if ( !app(walletInterface::class)->updateByConditions($walletAttributes, $walletConditions) )
                    {
                        throw new JobException(__("Withdrawal ID :id does not exists or the transaction is already processed as failed.", ['id' => $ipnResponse['result']['id']]));
                    }

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
                        'data' => __("Your withdrawal request of :amount :stockItem to :address has been failed.", ['amount' => $withdrawal->amount, 'stockItem' => $withdrawal->stockItem->item, 'address' => $withdrawal->address])
                    ]);

                    DB::commit();

                    return true;
                }

                return null;
            } catch (\Exception $exception) {
                DB::rollBack();

                logs()->error($exception->getMessage());

                return false;
            }
        }
    }

    public function deposit($ipnResponse, $wallet, $deposited)
    {
        if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
        {
            logs()->info('log: IPN payment is completed and deposit status is found as pending.');
        }

        $getActualPaidAmount = bcsub($ipnResponse['result']['amount'], $ipnResponse['result']['fee']);
        $depositSystemFee = bcdiv(bcmul($getActualPaidAmount, $wallet->stockItem->deposit_fee), "100");
        $amount = bcsub($getActualPaidAmount, $depositSystemFee);
        // update deposit status
        $depositAttributes = [
            'status' => PAYMENT_COMPLETED,
            'system_fee' => $depositSystemFee,
            'payment_method' => $ipnResponse['result']['payment_method'],
            'network_fee' => $ipnResponse['result']['fee'],
        ];

        if ( !app(DepositInterface::class)->updateByConditions($depositAttributes, ['id' => $deposited->id]) )
        {
            return [
                SERVICE_RESPONSE_STATUS => SERVICE_RESPONSE_ERROR,
                SERVICE_RESPONSE_MESSAGE => __('Failed to update deposit status to complete.'),
            ];
        }

        if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
        {
            logs()->info('log: Deposit Status is updated as completed successfully.');
        }

        $attributes = ['primary_balance' => DB::raw('primary_balance + ' . $amount)];
        // update relevant wallet
        if ( !app(WalletInterface::class)->updateByConditions($attributes, ['id' => $wallet->id]) )
        {
            return [
                SERVICE_RESPONSE_STATUS => SERVICE_RESPONSE_ERROR,
                SERVICE_RESPONSE_MESSAGE => __('Failed to update wallet balance.'),
            ];
        }

        if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true)
        {
            logs()->info('log: Wallet is updated with received amount.');
        }

        // make transaction
        $date = now();
        $transactionParameters = [
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($amount, '-1'),
                'journal' => DECREASED_FROM_OUTSIDE_ON_DEPOSIT_REQUEST,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($deposited),
                'model_id' => $deposited->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'journal' => INCREASED_TO_DEPOSIT_ON_DEPOSIT_REQUEST,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($deposited),
                'model_id' => $deposited->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($amount, '-1'),
                'journal' => DECREASED_FROM_DEPOSIT_ON_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $wallet->user_id,
                'stock_item_id' => $wallet->stock_item_id,
                'model_name' => get_class($wallet),
                'model_id' => $wallet->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'journal' => INCREASED_TO_WALLET_ON_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $deposited->user_id,
                'stock_item_id' => $deposited->stock_item_id,
                'model_name' => get_class($deposited),
                'model_id' => $deposited->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($depositSystemFee, '-1'),
                'journal' => DECREASED_FROM_DEPOSIT_AS_DEPOSIT_FEE_ON_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $deposited->user_id,
                'stock_item_id' => $deposited->stock_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $depositSystemFee,
                'journal' => INCREASED_TO_SYSTEM_AS_DEPOSIT_FEE_DEPOSIT_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ]
        ];
        app(TransactionInterface::class)->insert($transactionParameters);

        // notify user
        app(NotificationInterface::class)->create([
            'user_id' => $wallet->user_id,
            'data' => __("You've just received :amount :coin", ['amount' => $amount, 'coin' => $ipnResponse['result']['currency']])
        ]);

        // update deposit
        $stockItemAttributes = [
            'total_deposit' => DB::raw('total_deposit + ' . $amount),
            'total_deposit_fee' => DB::raw('total_deposit_fee + ' . $depositSystemFee)
        ];

        if ( !app(StockItemInterface::class)->update($stockItemAttributes, $wallet->stockItem->id) )
        {
            return [
                SERVICE_RESPONSE_STATUS => SERVICE_RESPONSE_ERROR,
                SERVICE_RESPONSE_MESSAGE => __('Failed to update deposit status.'),
            ];
        }
    }

    public function withdraw($withdrawal)
    {
        // make transaction
        $date = now();
        $transactionParameters = [
            [
                'user_id' => $withdrawal->user_id,
                'stock_item_id' => $withdrawal->stock_item_id,
                'model_name' => get_class($withdrawal),
                'model_id' => $withdrawal->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($withdrawal->amount, '-1'),
                'journal' => DECREASED_FROM_WITHDRAWAL_ON_WITHDRAWAL_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
            [
                'user_id' => $withdrawal->user_id,
                'stock_item_id' => $withdrawal->stock_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $withdrawal->amount,
                'journal' => INCREASED_TO_OUTSIDE_ON_WITHDRAWAL_CONFIRMATION,
                'updated_at' => $date,
                'created_at' => $date,
            ],
        ];
        app(TransactionInterface::class)->insert($transactionParameters);

        // notify user
        app(NotificationInterface::class)->create([
            'user_id' => $withdrawal->user_id,
            'data' => __("Your withdrawal request of :amount :stockItem to :address has been processed successfully.", ['amount' => $withdrawal->amount, 'stockItem' => $withdrawal->stockItem->item, 'address' => $withdrawal->address])
        ]);

        // update withdrawal info
        $stockItemAttributes = [
            'total_withdrawal' => DB::raw('total_withdrawal + ' . $withdrawal->amount),
            'total_withdrawal_fee' => DB::raw('total_withdrawal_fee + ' . $withdrawal->system_fee)
        ];

        app(StockItemInterface::class)->update( $stockItemAttributes, $withdrawal->stock_item_id );

        return [
            SERVICE_RESPONSE_STATUS => SERVICE_RESPONSE_SUCCESS,
        ];
    }

    public function reverseWithdraw($withdrawalId, $stockItem)
    {
        // update withdrawal status.
        $attributes = ['id' => $withdrawalId, 'status' => PAYMENT_FAILED];
        $conditions = ['id' => $withdrawalId, 'status' => PAYMENT_PENDING];
        $withdrawal = app(WithdrawalInterface::class)->updateByConditions($attributes, $conditions);

        if( $withdrawal )
        {
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
                'data' => __("Your withdrawal request of :amount :stockItem to :address has been failed.", ['amount' => $withdrawal->amount, 'stockItem' => $stockItem, 'address' => $withdrawal->address])
            ]);
        }
    }
}