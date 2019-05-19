<?php

namespace App\Services\Exchange;

use App\Events\Exchange\BroadcastPrivateSettlementOrder;
use App\Events\Exchange\BroadcastPrivateStockExchange;
use App\Events\Exchange\BroadcastSettlementOrders;
use App\Events\Exchange\BroadcastStockExchange;
use App\Events\Exchange\BroadcastStockPairSummary;
use App\Exceptions\JobException;
use App\Jobs\StopLimitStockOrder;
use App\Models\User\StockOrder;
use App\Models\User\Wallet;
use App\Repositories\Exchange\Interfaces\StockExchangeGroupInterface;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Trader\Interfaces\ReferralEarningInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockExchangeService
{
    private $stockOrder;
    private $stockPair;
    private $stockOrderRepository;

    private $stockItemType = null;
    private $baseItemType = null;

    private $primaryStockAmount = 0;
    private $processedPrimaryAmount = 0;
    private $primaryWalletAmount = 0;
    private $primaryWalletOnOrder = 0;


    private $buyStockOrder = null;
    private $sellStockOrder = null;
    private $exchangePrice = 0;
    private $exchangeAmount = 0;
    private $date;
    private $stockExchangeInputs = [];
    private $updateStockOrdersInputs = [];
    private $updateWalletInputs = [];
    private $makerFee = 0;
    private $takerFee = 0;
    private $transactionInputs = [];
    private $stockPairSummary = [];

    private $exchangedOrders = [];
    private $privateExchangedOrders = [];
    private $settlementOrders = [];
    private $privateSettlementOrders = [];

    private $userWalletData = [];
    private $referrerUsers = [];
    private $referralEarnings = [];

    public function __construct(StockOrder $stockOrder, StockOrderInterface $stockOrderRepository)
    {
        $this->date = Carbon::now();
        $this->stockOrder = $stockOrder;
        $this->stockOrderRepository = $stockOrderRepository;

        $this->stockPairSummary = [
            'base_item_buy_order_volume' => 0,
            'stock_item_buy_order_volume' => 0,
            'base_item_sale_order_volume' => 0,
            'stock_item_sale_order_volume' => 0,

            'exchanged_buy_total' => 0,
            'exchanged_sale_total' => 0,

            'exchanged_amount' => 0,
            'exchanged_maker_total' => 0,

            'exchanged_buy_fee' => 0,
            'exchanged_sale_fee' => 0,
            'last_price' => 0,
        ];
    }

    public function process()
    {

        $this->stockPair = app(StockPairInterface::class)->getFirstStockPairDetailByConditions(['stock_pairs.id' => $this->stockOrder->stock_pair_id]);
        $this->stockItemType = $this->stockPair->stock_item_type;
        $this->baseItemType = $this->stockPair->base_item_type;
        $oppositeStockOrders = $this->getOppositeStockOrders();


        if ($oppositeStockOrders->isEmpty()) {
            return true;
        }

        if ($this->stockOrder->exchange_type == EXCHANGE_BUY) {
            $this->buyStockOrder = $this->stockOrder;
        } else {
            $this->sellStockOrder = $this->stockOrder;
        }

        if (admin_settings('referral') && bccomp(admin_settings('referral_percentage'), "0")) {
            $allUsers[] = $this->stockOrder->user_id;
            $allUsers = array_unique(array_merge($allUsers, $oppositeStockOrders->pluck('user_id')->toArray()));

            $users = app(UserInterface::class)->getByUserIds($allUsers);
            $this->referrerUsers = $users->pluck('referrer_id', 'id')->toArray();

        }

        DB::beginTransaction();

        try {
            $stockExchangeGroup = app(StockExchangeGroupInterface::class)->create([]);

            if (empty($stockExchangeGroup)) {
                throw new JobException('Could not create stock exchange group');
            }

            $lastOppositeStockOrder = null;

            foreach ($oppositeStockOrders as $oppositeStockOrder) {

                $oppositeStockOrder->exchange_type == EXCHANGE_BUY ? $this->buyStockOrder = $oppositeStockOrder : $this->sellStockOrder = $oppositeStockOrder;

                $successfulOrder = $this->processStockOrderInputs();
                if (!$successfulOrder) {
                    break;
                }
                $this->processStockExchangeInputs($stockExchangeGroup);

                $this->processWalletInputs();

                if (admin_settings('referral') && bccomp(admin_settings('referral_percentage'), "0")) {
                    $this->processReferralEarning();
                }

                $lastOppositeStockOrder = $oppositeStockOrder;

            }


            $this->oppositeStockOrderSettlementProcess($lastOppositeStockOrder);
            $this->primaryStockOrderSettlementProcess();


            $isStockOrdersUpdate = $this->stockOrderRepository->bulkUpdate($this->updateStockOrdersInputs);
            if ($isStockOrdersUpdate != count($this->updateStockOrdersInputs)) {
                throw new JobException('Failed to update stock orders.');
            }

            $stockExchangeInserted = app(StockExchangeInterface::class)->insert($this->stockExchangeInputs);
            if (!$stockExchangeInserted) {
                throw new JobException('Failed to insert stock exchanges.');
            }

            $isWalletUpdated = app(WalletInterface::class)->bulkUpdate($this->updateWalletInputs);
            if ($isWalletUpdated != count($this->updateWalletInputs)) {
                throw new JobException('Failed to update users wallet.');
            }

            $this->processTransactionInputs($stockExchangeGroup);
            $isTransactionInserted = app(TransactionInterface::class)->insert($this->transactionInputs);
            if (!$isTransactionInserted) {
                throw new JobException('Failed to insert transactions.');
            }

            if (!$this->updateCoinPair()) {
                throw new JobException('Failed to update stock pair.');
            }

            if (!empty($this->referralEarnings)) {
                $referralEarningCount = app(ReferralEarningInterface::class)->insert($this->referralEarnings);
                if ($referralEarningCount != count($this->referralEarnings)) {
                    throw new JobException('Failed to insert referral earning.');
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            logs()->error("Exchange Failed: " . $e->getMessage());
        }

        $payloadData = [
            'exchangedOrders' => $this->exchangedOrders,
            'stockPairSummary' => $this->stockPairSummary,
            'chartData' => [
                'price' => $this->exchangePrice,
                'date' => $this->date->toDateTimeString(),
                'interval' => $this->getTimeIntervals($this->date->copy())
            ]
        ];

        //Broadcast StockPairSummary
        broadcast(new BroadcastStockPairSummary($this->stockPairSummary));

        //Broadcast Exchanged Data
        broadcast(new BroadcastStockExchange($this->stockPair->id, $payloadData));

        //Broadcast user private exchanged data
        foreach ($this->privateExchangedOrders as $userId => $userExchangedOrders) {
            broadcast(new BroadcastPrivateStockExchange($this->stockPair->id, $userId, $userExchangedOrders));
        }

        //Broadcast settlement orders
        if (!empty($this->settlementOrders)) {
            broadcast(new BroadcastSettlementOrders($this->stockPair->id, $this->settlementOrders));
        }

        //Broadcast private settlement orders
        if (!empty($this->privateSettlementOrders)) {
            foreach ($this->privateSettlementOrders as $userId => $privateSettlementOrders)
                broadcast(new BroadcastPrivateSettlementOrder($this->stockPair->id, $userId, $privateSettlementOrders));
        }

        //Check price change
        if (bccomp($this->stockPair->last_price, $this->exchangePrice) != 0) {

            app(StockGraphDataService::class)->process($this->stockPair->id, $this->exchangePrice, $this->date);

            dispatch(new StopLimitStockOrder($this->stockPair->id, $this->exchangePrice));
        }

    }

    private function getOppositeStockOrders()
    {
        return $this->stockOrderRepository->getOppositeStockOrders($this->stockOrder);
    }

    private function processStockOrderInputs()
    {
        $minimumTransactionFee = $this->minimumTransactionFee($this->baseItemType);
        $minimumTotal = get_minimum_order_total($minimumTransactionFee);

        if ($this->stockOrder->id == $this->buyStockOrder->id) {
            $exchangeableBuyAmount = bcsub(bcsub($this->buyStockOrder->amount, $this->buyStockOrder->exchanged), $this->processedPrimaryAmount);
            $unprocessedAmount = $exchangeableBuyAmount;
            $unprocessedTotal = bcmul($exchangeableBuyAmount, $this->stockOrder->price);

            $exchangeableSellAmount = bcsub($this->sellStockOrder->amount, $this->sellStockOrder->exchanged);
        } else {
            $exchangeableBuyAmount = bcsub($this->buyStockOrder->amount, $this->buyStockOrder->exchanged);

            $exchangeableSellAmount = bcsub(bcsub($this->sellStockOrder->amount, $this->sellStockOrder->exchanged), $this->processedPrimaryAmount);
            $unprocessedAmount = $exchangeableSellAmount;
            $unprocessedTotal = bcmul($exchangeableSellAmount, $this->stockOrder->price);
        }
        if (bccomp($unprocessedAmount, '0') > 0 && bccomp($minimumTotal, $unprocessedTotal) > 0) {
            return false;
        }

        if (bccomp($exchangeableBuyAmount, $exchangeableSellAmount) > 0) {
            $this->exchangeAmount = $exchangeableSellAmount;
            $sellOrderStatus = STOCK_ORDER_COMPLETED;
            $buyOrderStatus = STOCK_ORDER_PENDING;
            $this->_stockOrderInputSubProcess($sellOrderStatus, $buyOrderStatus);
        } elseif (bccomp($exchangeableBuyAmount, $exchangeableSellAmount) < 0) {
            $this->exchangeAmount = $exchangeableBuyAmount;
            $sellOrderStatus = STOCK_ORDER_PENDING;
            $buyOrderStatus = STOCK_ORDER_COMPLETED;
            $this->_stockOrderInputSubProcess($sellOrderStatus, $buyOrderStatus);
        } else {
            $this->exchangeAmount = $exchangeableBuyAmount;
            $sellOrderStatus = STOCK_ORDER_COMPLETED;
            $buyOrderStatus = STOCK_ORDER_COMPLETED;
            $this->_stockOrderInputSubProcess($sellOrderStatus, $buyOrderStatus);
        }
        $this->processedPrimaryAmount = bcadd($this->processedPrimaryAmount, $this->exchangeAmount);
        return true;
    }

    private function minimumTransactionFee($type)
    {
        return $type == CURRENCY_REAL ? MINIMUM_TRANSACTION_FEE_CURRENCY : MINIMUM_TRANSACTION_FEE_CRYPTO;
    }

    private function _stockOrderInputSubProcess($sellOrderStatus, $buyOrderStatus)
    {
        if ($this->stockOrder->id == $this->sellStockOrder->id) {
            $this->primaryStockAmount = bcadd($this->primaryStockAmount, $this->exchangeAmount);
        } else {
            array_push($this->updateStockOrdersInputs, [
                'conditions' => ['id' => $this->sellStockOrder->id, 'status' => STOCK_ORDER_PENDING],
                'fields' => [
                    'status' => $sellOrderStatus,
                    'exchanged' => ['increment', $this->exchangeAmount],
                ]
            ]);
        }
        if ($this->stockOrder->id == $this->buyStockOrder->id) {
            $this->primaryStockAmount = bcadd($this->primaryStockAmount, $this->exchangeAmount);
        } else {
            array_push($this->updateStockOrdersInputs, [
                'conditions' => ['id' => $this->buyStockOrder->id, 'status' => STOCK_ORDER_PENDING],
                'fields' => [
                    'status' => $buyOrderStatus,
                    'exchanged' => ['increment', $this->exchangeAmount]
                ]
            ]);
        }
    }

    private function processStockExchangeInputs($stockExchangeGroup)
    {
        $adminSettings = admin_settings(['exchange_maker_fee', 'exchange_taker_fee']);
        $makerFeeInPercentage = $adminSettings['exchange_maker_fee'];
        $takerFeeInPercentage = $adminSettings['exchange_taker_fee'];

        if ($this->buyStockOrder->created_at < $this->sellStockOrder->created_at) {
            $this->exchangePrice = $this->buyStockOrder->price;

            $this->makerFee = calculate_exchange_fee($this->exchangeAmount, $makerFeeInPercentage);
            $this->takerFee = calculate_exchange_fee(bcmul($this->exchangeAmount, $this->sellStockOrder->price), $takerFeeInPercentage);

            $buyStockExchangeInput = [
                'user_id' => $this->buyStockOrder->user_id,
                'stock_exchange_group_id' => $stockExchangeGroup->id,
                'stock_order_id' => $this->buyStockOrder->id,
                'stock_pair_id' => $this->buyStockOrder->stock_pair_id,
                'amount' => $this->exchangeAmount,
                'price' => $this->buyStockOrder->price,
                'total' => bcmul($this->exchangeAmount, $this->buyStockOrder->price),
                'fee' => $this->makerFee,
                'referral_earning' => 0,
                'exchange_type' => $this->buyStockOrder->exchange_type,
                'related_order_id' => $this->sellStockOrder->id,
                'is_maker' => true,
                'created_at' => $this->date,
                'updated_at' => $this->date
            ];

            $sellStockExchangeInput = [
                'user_id' => $this->sellStockOrder->user_id,
                'stock_exchange_group_id' => $stockExchangeGroup->id,
                'stock_order_id' => $this->sellStockOrder->id,
                'stock_pair_id' => $this->sellStockOrder->stock_pair_id,
                'amount' => $this->exchangeAmount,
                'price' => $this->sellStockOrder->price,
                'total' => bcmul($this->exchangeAmount, $this->sellStockOrder->price),
                'fee' => $this->takerFee,
                'referral_earning' => 0,
                'exchange_type' => $this->sellStockOrder->exchange_type,
                'related_order_id' => $this->buyStockOrder->id,
                'is_maker' => false,
                'created_at' => $this->date,
                'updated_at' => $this->date
            ];

            if (admin_settings('referral') && bccomp(admin_settings('referral_percentage'), "0")) {
                $referralPercentage = admin_settings('referral_percentage');
                if (!empty($this->referrerUsers[$this->buyStockOrder->user_id])) {
                    $buyerReferrerEarning = bcdiv(bcmul($this->makerFee, $referralPercentage), "100");
                    $buyerFee = bcsub($this->makerFee, $buyerReferrerEarning);

                    $buyStockExchangeInput['fee'] = $buyerFee;
                    $buyStockExchangeInput['referral_earning'] = $buyerReferrerEarning;
                }

                if (!empty($this->referrerUsers[$this->sellStockOrder->user_id])) {
                    $sellerReferrerEarning = bcdiv(bcmul($this->takerFee, $referralPercentage), "100");
                    $sellerFee = bcsub($this->takerFee, $sellerReferrerEarning);

                    $sellStockExchangeInput['fee'] = $sellerFee;
                    $sellStockExchangeInput['referral_earning'] = $sellerReferrerEarning;
                }
            }

        } else {
            $this->exchangePrice = $this->sellStockOrder->price;

            $this->takerFee = calculate_exchange_fee($this->exchangeAmount, $takerFeeInPercentage);
            $this->makerFee = calculate_exchange_fee(bcmul($this->exchangeAmount, $this->sellStockOrder->price), $makerFeeInPercentage);

            $buyStockExchangeInput = [
                'user_id' => $this->buyStockOrder->user_id,
                'stock_exchange_group_id' => $stockExchangeGroup->id,
                'stock_order_id' => $this->buyStockOrder->id,
                'stock_pair_id' => $this->buyStockOrder->stock_pair_id,
                'amount' => $this->exchangeAmount,
                'price' => $this->buyStockOrder->price,
                'total' => bcmul($this->exchangeAmount, $this->buyStockOrder->price),
                'fee' => $this->takerFee,
                'referral_earning' => 0,
                'exchange_type' => $this->buyStockOrder->exchange_type,
                'related_order_id' => $this->sellStockOrder->id,
                'is_maker' => false,
                'created_at' => $this->date,
                'updated_at' => $this->date
            ];

            $sellStockExchangeInput = [
                'user_id' => $this->sellStockOrder->user_id,
                'stock_exchange_group_id' => $stockExchangeGroup->id,
                'stock_order_id' => $this->sellStockOrder->id,
                'stock_pair_id' => $this->sellStockOrder->stock_pair_id,
                'amount' => $this->exchangeAmount,
                'price' => $this->sellStockOrder->price,
                'total' => bcmul($this->exchangeAmount, $this->sellStockOrder->price),
                'fee' => $this->makerFee,
                'referral_earning' => 0,
                'exchange_type' => $this->sellStockOrder->exchange_type,
                'related_order_id' => $this->buyStockOrder->id,
                'is_maker' => true,
                'created_at' => $this->date,
                'updated_at' => $this->date
            ];

            if (admin_settings('referral') && bccomp(admin_settings('referral_percentage'), "0")) {
                $referralPercentage = admin_settings('referral_percentage');

                if (!empty($this->referrerUsers[$this->buyStockOrder->user_id])) {
                    $buyerReferrerEarning = bcdiv(bcmul($this->takerFee, $referralPercentage), "100");
                    $buyerFee = bcsub($this->takerFee, $buyerReferrerEarning);

                    $buyStockExchangeInput['fee'] = $buyerFee;
                    $buyStockExchangeInput['referral_earning'] = $buyerReferrerEarning;
                }

                if (!empty($this->referrerUsers[$this->sellStockOrder->user_id])) {
                    $sellerReferrerEarning = bcdiv(bcmul($this->makerFee, $referralPercentage), "100");
                    $sellerFee = bcsub($this->makerFee, $sellerReferrerEarning);

                    $sellStockExchangeInput['fee'] = $sellerFee;
                    $sellStockExchangeInput['referral_earning'] = $sellerReferrerEarning;
                }

            }
        }

        array_push($this->stockExchangeInputs, $sellStockExchangeInput, $buyStockExchangeInput);

    }

    private function processWalletInputs()
    {
        // Buy Order
        $buyerFee = $this->buyStockOrder->created_at > $this->sellStockOrder->created_at ? $this->takerFee : $this->makerFee;
        $buyerIncrementAmount = bcsub($this->exchangeAmount, $buyerFee);

        if ($this->stockOrder->id == $this->buyStockOrder->id) {
            $this->primaryWalletAmount = bcadd($this->primaryWalletAmount, $buyerIncrementAmount);
            $this->primaryWalletOnOrder = bcadd(bcmul($this->exchangeAmount, $this->buyStockOrder->price), $this->primaryWalletOnOrder);
        } else {
            // Increase Primary Order
            if (isset($this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->stock_item_id])) {
                $index = $this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->stock_item_id];
                if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                    $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                    $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $buyerIncrementAmount);
                } else {
                    $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $buyerIncrementAmount];
                }
            } else {
                array_push($this->updateWalletInputs, [
                    'conditions' => ['user_id' => $this->buyStockOrder->user_id, 'stock_item_id' => $this->stockPair->stock_item_id],
                    'fields' => [
                        'primary_balance' => ['increment', $buyerIncrementAmount],
                    ]
                ]);

                $this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->stock_item_id] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
            }

            // Decrease On Order
            if (isset($this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->base_item_id])) {
                $index = $this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->base_item_id];
                if (isset($this->updateWalletInputs[$index]['fields']['on_order_balance'])) {
                    $amount = $this->updateWalletInputs[$index]['fields']['on_order_balance'][1];
                    $this->updateWalletInputs[$index]['fields']['on_order_balance'][1] = bcadd($amount, bcmul($this->buyStockOrder->price, $this->exchangeAmount));
                } else {
                    $this->updateWalletInputs[$index]['fields']['on_order_balance'] = ['decrement', bcmul($this->buyStockOrder->price, $this->exchangeAmount)];
                }
            } else {
                array_push($this->updateWalletInputs, [
                    'conditions' => ['user_id' => $this->buyStockOrder->user_id, 'stock_item_id' => $this->stockPair->base_item_id],
                    'fields' => [
                        'on_order_balance' => ['decrement', bcmul($this->buyStockOrder->price, $this->exchangeAmount)]
                    ]
                ]);
                if (!isset($this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->base_item_id])) {
                    $this->userWalletData[$this->buyStockOrder->user_id][$this->stockPair->base_item_id] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
                }
            }
        }

        // Sell order
        $sellerFee = $this->sellStockOrder->created_at > $this->buyStockOrder->created_at ? $this->takerFee : $this->makerFee;
        $sellerIncrementAmount = bcsub(bcmul($this->exchangeAmount, $this->sellStockOrder->price), $sellerFee);

        if ($this->stockOrder->id == $this->sellStockOrder->id) {
            $this->primaryWalletAmount = bcadd($this->primaryWalletAmount, $sellerIncrementAmount);
            $this->primaryWalletOnOrder = bcadd($this->exchangeAmount, $this->primaryWalletOnOrder);
        } else {
            // Increase Primary balance
            if (isset($this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->base_item_id])) {
                $index = $this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->base_item_id];
                if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                    $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                    $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $sellerIncrementAmount);
                } else {
                    $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $sellerIncrementAmount];
                }
            } else {
                array_push($this->updateWalletInputs, [
                    'conditions' => ['user_id' => $this->sellStockOrder->user_id, 'stock_item_id' => $this->stockPair->base_item_id],
                    'fields' => [
                        'primary_balance' => ['increment', $sellerIncrementAmount]
                    ]
                ]);
                if (!isset($this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->base_item_id])) {
                    $this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->base_item_id] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
                }
            }

            // Decrease On Order
            if (isset($this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->stock_item_id])) {
                $index = $this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->stock_item_id];

                if (isset($this->updateWalletInputs[$index]['fields']['on_order_balance'])) {
                    $amount = $this->updateWalletInputs[$index]['fields']['on_order_balance'][1];
                    $this->updateWalletInputs[$index]['fields']['on_order_balance'][1] = bcadd($amount, $this->exchangeAmount);
                } else {
                    $this->updateWalletInputs[$index]['fields']['on_order_balance'] = ['decrement', $this->exchangeAmount];
                }
            } else {
                array_push($this->updateWalletInputs, [
                    'conditions' => ['user_id' => $this->sellStockOrder->user_id, 'stock_item_id' => $this->stockPair->stock_item_id],
                    'fields' => [
                        'on_order_balance' => ['decrement', $this->exchangeAmount]
                    ]
                ]);
                if (!isset($this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->stock_item_id])) {
                    $this->userWalletData[$this->sellStockOrder->user_id][$this->stockPair->stock_item_id] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
                }
            }
        }
    }

    private function processReferralEarning()
    {
        $referralPercentage = admin_settings('referral_percentage');
        $buyerReferrer = $this->referrerUsers[$this->buyStockOrder->user_id];

        if (!empty($buyerReferrer)) {
            $buyerFee = $this->buyStockOrder->created_at > $this->sellStockOrder->created_at ? $this->takerFee : $this->makerFee;
            $referralEarning = bcdiv(bcmul($buyerFee, $referralPercentage), '100');

            if (isset($this->userWalletData[$buyerReferrer][$this->stockPair->stock_item_id])) {
                $index = $this->userWalletData[$buyerReferrer][$this->stockPair->stock_item_id];
                if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                    $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                    $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $referralEarning);
                } else {
                    $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $referralEarning];
                }
            } else {
                array_push($this->updateWalletInputs, [
                    'conditions' => ['user_id' => $buyerReferrer, 'stock_item_id' => $this->stockPair->stock_item_id],
                    'fields' => [
                        'primary_balance' => ['increment', $referralEarning]
                    ]
                ]);

                $this->userWalletData[$buyerReferrer][$this->stockPair->stock_item_id] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
            }

            array_push($this->referralEarnings, [
                'referrer_user_id' => $buyerReferrer,
                'referral_user_id' => $this->buyStockOrder->user_id,
                'stock_item_id' => $this->stockPair->stock_item_id,
                'amount' => $referralEarning,
                'created_at' => $this->date,
                'updated_at' => $this->date
            ]);

        }

        $sellerReferrer = $this->referrerUsers[$this->sellStockOrder->user_id];
        if (!empty($sellerReferrer)) {
            $sellerFee = $this->sellStockOrder->created_at > $this->buyStockOrder->created_at ? $this->takerFee : $this->makerFee;
            $referralEarning = bcdiv(bcmul($sellerFee, $referralPercentage), '100');

            if (isset($this->userWalletData[$sellerReferrer][$this->stockPair->base_item_id])) {
                $index = $this->userWalletData[$sellerReferrer][$this->stockPair->base_item_id];
                if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                    $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                    $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $referralEarning);
                } else {
                    $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $referralEarning];
                }
            } else {
                array_push($this->updateWalletInputs, [
                    'conditions' => ['user_id' => $sellerReferrer, 'stock_item_id' => $this->stockPair->base_item_id],
                    'fields' => [
                        'primary_balance' => ['increment', $referralEarning]
                    ]
                ]);

                $this->userWalletData[$sellerReferrer][$this->stockPair->base_item_id] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
            }

            array_push($this->referralEarnings, [
                'referrer_user_id' => $sellerReferrer,
                'referral_user_id' => $this->sellStockOrder->user_id,
                'stock_item_id' => $this->stockPair->base_item_id,
                'amount' => $referralEarning,
                'created_at' => $this->date,
                'updated_at' => $this->date
            ]);
        }
    }

    private function oppositeStockOrderSettlementProcess($oppositeStockOrder)
    {
        $unprocessedAmount = bcsub(bcsub($oppositeStockOrder->amount, $oppositeStockOrder->exchanged), $this->exchangeAmount);
        $minimumTransactionFee = $this->minimumTransactionFee($this->baseItemType);
        $minimumTotal = get_minimum_order_total($minimumTransactionFee);
        $unprocessedTotal = bcmul($unprocessedAmount, $oppositeStockOrder->price);
        $key = count($this->updateStockOrdersInputs) - 1;


        if (bccomp($unprocessedAmount, '0') > 0) {
            if (bccomp($minimumTotal, $unprocessedTotal) > 0) {
                $this->updateStockOrdersInputs[$key]['fields']['status'] = STOCK_ORDER_COMPLETED;
                $this->updateStockOrdersInputs[$key]['fields']['canceled'] = $unprocessedAmount;


                //Wallet starts
                $stockItemId = $this->stockPair->stock_item_id;
                $incrementAmount = $returnAmount = $unprocessedAmount;
                $returnTotal = bcmul($returnAmount, $this->stockOrder->price);
                $pairOnOrderStockSettlement = 'stock_item_sale_order_volume';
                $pairOnOrderBaseSettlement = 'base_item_sale_order_volume';

                if ($oppositeStockOrder->exchange_type == EXCHANGE_BUY) {
                    $stockItemId = $this->stockPair->base_item_id;
                    $incrementAmount = bcmul($unprocessedAmount, $oppositeStockOrder->price);
                    $pairOnOrderStockSettlement = 'stock_item_buy_order_volume';
                    $pairOnOrderBaseSettlement = 'base_item_buy_order_volume';

                }


                $this->stockPairSummary[$pairOnOrderStockSettlement] = $returnAmount;
                $this->stockPairSummary[$pairOnOrderBaseSettlement] = $returnTotal;


                if (isset($this->userWalletData[$oppositeStockOrder->user_id][$stockItemId])) {
                    $index = $this->userWalletData[$oppositeStockOrder->user_id][$stockItemId];

                    if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                        $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                        $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $incrementAmount);
                    } else {
                        $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $incrementAmount];
                    }
                    if (isset($this->updateWalletInputs[$index]['fields']['on_order_balance'])) {
                        $amountOnOrder = $this->updateWalletInputs[$index]['fields']['on_order_balance'][1];
                        $this->updateWalletInputs[$index]['fields']['on_order_balance'][1] = bcadd($amountOnOrder, $incrementAmount);
                    } else {
                        $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['decrement', $incrementAmount];
                    }
                } else {
                    array_push($this->updateWalletInputs, [
                        'conditions' => ['user_id' => $oppositeStockOrder->user_id, 'stock_item_id' => $stockItemId],
                        'fields' => [
                            'primary_balance' => ['increment', $incrementAmount],
                            'on_order_balance' => ['decrement', $incrementAmount]
                        ]
                    ]);
                    if (!isset($this->userWalletData[$oppositeStockOrder->user_id][$stockItemId])) {
                        $this->userWalletData[$oppositeStockOrder->user_id][$stockItemId] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
                    }
                }

                //Broadcast settlement data
                $this->settlementOrders = [
                    'exchange_type' => $oppositeStockOrder->exchange_type,
                    'price' => $oppositeStockOrder->price,
                    'amount' => bcmul($unprocessedAmount, '-1'),
                    'total' => bcmul(bcmul($unprocessedAmount, $oppositeStockOrder->price), '-1')
                ];

                //Broadcast private settlement data
                $this->privateSettlementOrders[$oppositeStockOrder->user_id][] = [
                    'order_number' => $oppositeStockOrder->id,
                    'exchange_type' => $oppositeStockOrder->exchange_type,
                    'price' => $oppositeStockOrder->price,
                    'amount' => $unprocessedAmount,
                    'total' => bcmul($unprocessedAmount, $oppositeStockOrder->price)
                ];

                $fromOrderToWallet = [
                    'user_id' => $oppositeStockOrder->user_id,
                    'stock_item_id' => $stockItemId,
                    'model_name' => get_class($oppositeStockOrder),
                    'model_id' => $oppositeStockOrder->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($incrementAmount, '-1'),
                    'journal' => DECREASED_FROM_ORDER_ON_SETTLEMENT,
                    'created_at' => $this->date,
                    'updated_at' => $this->date

                ];

                $toWalletFromOrder = [
                    'user_id' => $oppositeStockOrder->user_id,
                    'stock_item_id' => $stockItemId,
                    'model_name' => get_class(new Wallet()),
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $incrementAmount,
                    'journal' => INCREASED_TO_WALLET_ON_SETTLEMENT,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                array_push($this->transactionInputs, $fromOrderToWallet, $toWalletFromOrder);

                //Wallet ends
            }
        } else {
            $this->updateStockOrdersInputs[$key]['fields']['status'] = STOCK_ORDER_COMPLETED;
        }
    }

    private function primaryStockOrderSettlementProcess()
    {
        $unprocessedAmount = bcsub(bcsub($this->stockOrder->amount, $this->stockOrder->exchanged), $this->primaryStockAmount);
        $minimumTransactionFee = $this->minimumTransactionFee($this->baseItemType);
        $minimumTotal = get_minimum_order_total($minimumTransactionFee);
        $unprocessedTotal = bcmul($unprocessedAmount, $this->stockOrder->price);

        $primaryStockItemId = $this->stockOrder->exchange_type == EXCHANGE_BUY ? $this->stockPair->stock_item_id : $this->stockPair->base_item_id;
        $primaryStockOnOrderItemId = $this->stockOrder->exchange_type == EXCHANGE_BUY ? $this->stockPair->base_item_id : $this->stockPair->stock_item_id;

        if (isset($this->userWalletData[$this->stockOrder->user_id][$primaryStockItemId])) {
            $index = $this->userWalletData[$this->stockOrder->user_id][$primaryStockItemId];
            if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $this->primaryWalletAmount);
            } else {
                $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $this->primaryWalletAmount];
            }
        } else {
            array_push($this->updateWalletInputs, [
                'conditions' => ['user_id' => $this->stockOrder->user_id, 'stock_item_id' => $primaryStockItemId],
                'fields' => [
                    'primary_balance' => ['increment', $this->primaryWalletAmount]
                ]
            ]);
            if (!isset($this->userWalletData[$this->stockOrder->user_id][$primaryStockItemId])) {
                $this->userWalletData[$this->stockOrder->user_id][$primaryStockItemId] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
            }
        }
        // Order
        if (isset($this->userWalletData[$this->stockOrder->user_id][$primaryStockOnOrderItemId])) {
            $index = $this->userWalletData[$this->stockOrder->user_id][$primaryStockOnOrderItemId];

            if (isset($this->updateWalletInputs[$index]['fields']['on_order_balance'])) {
                $amount = $this->updateWalletInputs[$index]['fields']['on_order_balance'][1];
                $this->updateWalletInputs[$index]['fields']['on_order_balance'][1] = bcadd($amount, $this->primaryWalletOnOrder);
            } else {
                $this->updateWalletInputs[$index]['fields']['on_order_balance'] = ['decrement', $this->primaryWalletOnOrder];
            }

        } else {
            array_push($this->updateWalletInputs, [
                'conditions' => ['user_id' => $this->stockOrder->user_id, 'stock_item_id' => $primaryStockOnOrderItemId],
                'fields' => [
                    'on_order_balance' => ['decrement', $this->primaryWalletOnOrder]
                ]
            ]);

            if (!isset($this->userWalletData[$this->stockOrder->user_id][$primaryStockOnOrderItemId])) {
                $this->userWalletData[$this->stockOrder->user_id][$primaryStockOnOrderItemId] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
            }
        }


        $primaryStockOrder = [
            'conditions' => ['id' => $this->stockOrder->id, 'status' => STOCK_ORDER_PENDING],
            'fields' => [
                'status' => STOCK_ORDER_PENDING,
                'exchanged' => ['increment', $this->primaryStockAmount]
            ]
        ];


        if (bccomp($unprocessedAmount, '0') > 0) {
            if (bccomp($minimumTotal, $unprocessedTotal) > 0) {
                $primaryStockOrder['fields']['status'] = STOCK_ORDER_COMPLETED;
                $primaryStockOrder['fields']['canceled'] = $unprocessedAmount;


                //Wallet starts
                $stockItemId = $this->stockPair->stock_item_id;
                $incrementAmount = $returnAmount = $unprocessedAmount;
                $returnTotal = bcmul($returnAmount, $this->stockOrder->price);
                $pairOnOrderStockSettlement = 'stock_item_sale_order_volume';
                $pairOnOrderBaseSettlement = 'base_item_sale_order_volume';

                if ($this->stockOrder->exchange_type == EXCHANGE_BUY) {
                    $stockItemId = $this->stockPair->base_item_id;
                    $incrementAmount = bcmul($unprocessedAmount, $this->stockOrder->price);
                    $pairOnOrderStockSettlement = 'stock_item_buy_order_volume';
                    $pairOnOrderBaseSettlement = 'base_item_buy_order_volume';

                }


                $this->stockPairSummary[$pairOnOrderStockSettlement] = $returnAmount;
                $this->stockPairSummary[$pairOnOrderBaseSettlement] = $returnTotal;


                if (isset($this->userWalletData[$this->stockOrder->user_id][$stockItemId])) {
                    $index = $this->userWalletData[$this->stockOrder->user_id][$stockItemId];

                    if (isset($this->updateWalletInputs[$index]['fields']['primary_balance'])) {
                        $amount = $this->updateWalletInputs[$index]['fields']['primary_balance'][1];
                        $this->updateWalletInputs[$index]['fields']['primary_balance'][1] = bcadd($amount, $incrementAmount);
                    } else {
                        $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['increment', $incrementAmount];
                    }
                    if (isset($this->updateWalletInputs[$index]['fields']['on_order_balance'])) {
                        $amountOnOrder = $this->updateWalletInputs[$index]['fields']['on_order_balance'][1];
                        $this->updateWalletInputs[$index]['fields']['on_order_balance'][1] = bcadd($amountOnOrder, $incrementAmount);
                    } else {
                        $this->updateWalletInputs[$index]['fields']['primary_balance'] = ['decrement', $incrementAmount];
                    }
                } else {
                    array_push($this->updateWalletInputs, [
                        'conditions' => ['user_id' => $this->stockOrder->user_id, 'stock_item_id' => $stockItemId],
                        'fields' => [
                            'primary_balance' => ['increment', $incrementAmount],
                            'on_order_balance' => ['decrement', $incrementAmount]
                        ]
                    ]);
                    if (!isset($this->userWalletData[$this->stockOrder->user_id][$stockItemId])) {
                        $this->userWalletData[$this->stockOrder->user_id][$stockItemId] = key(array_slice($this->updateWalletInputs, -1, 1, TRUE));
                    }
                }


                //Broadcast settlement data
                $this->settlementOrders = [
                    'exchange_type' => $this->stockOrder->exchange_type,
                    'price' => $this->stockOrder->price,
                    'amount' => bcmul($unprocessedAmount, '-1'),
                    'total' => bcmul(bcmul($unprocessedAmount, $this->stockOrder->price), '-1')
                ];

                //Broadcast private settlement data
                $this->privateSettlementOrders[$this->stockOrder->user_id][] = [
                    'order_number' => $this->stockOrder->id,
                    'exchange_type' => $this->stockOrder->exchange_type,
                    'price' => $this->stockOrder->price,
                    'amount' => $unprocessedAmount,
                    'total' => bcmul($unprocessedAmount, $this->stockOrder->price)
                ];


                $fromOrderToWallet = [
                    'user_id' => $this->stockOrder->user_id,
                    'stock_item_id' => $stockItemId,
                    'model_name' => get_class($this->stockOrder),
                    'model_id' => $this->stockOrder->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($incrementAmount, '-1'),
                    'journal' => DECREASED_FROM_ORDER_ON_SETTLEMENT,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toWalletFromOrder = [
                    'user_id' => $this->stockOrder->user_id,
                    'stock_item_id' => $stockItemId,
                    'model_name' => get_class(new Wallet()),
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $incrementAmount,
                    'journal' => INCREASED_TO_WALLET_ON_SETTLEMENT,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                array_push($this->transactionInputs, $fromOrderToWallet, $toWalletFromOrder);
                //Wallet ends
            }
        } else {
            $primaryStockOrder['fields']['status'] = STOCK_ORDER_COMPLETED;
        }
        array_push($this->updateStockOrdersInputs, $primaryStockOrder);
    }

    private function processTransactionInputs($stockExchangeGroup)
    {
        $stockExchanges = app(StockExchangeInterface::class)->getByConditions(['stock_exchange_group_id' => $stockExchangeGroup->id]);
        $this->stockPairSummary['last_price'] = $this->exchangePrice;

        foreach ($stockExchanges as $stockExchange) {
            $this->summaryReportDevelopment($stockExchange);

            if ($stockExchange->exchange_type == EXCHANGE_BUY) {

                $fromOrderToTransaction = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->base_item_id,
                    'model_name' => get_class($this->stockOrder),
                    'model_id' => $stockExchange->stock_order_id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($stockExchange->total, '-1'),
                    'journal' => DECREASED_FROM_ORDER_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toTransactionFromOrder = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->base_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $stockExchange->total,
                    'journal' => INCREASED_TO_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $fromTransactionToWallet = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->stock_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul(bcsub($stockExchange->amount, $stockExchange->fee), '-1'),
                    'journal' => DECREASED_FROM_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toWalletFromTransaction = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->stock_item_id,
                    'model_name' => get_class(new Wallet()),
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => bcsub($stockExchange->amount, $stockExchange->fee),
                    'journal' => INCREASED_TO_WALLET_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $fromTransactionToSystem = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->stock_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($stockExchange->fee, '-1'),
                    'journal' => DECREASED_FROM_EXCHANGE_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toSystemFromTransaction = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->stock_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $stockExchange->fee,
                    'journal' => INCREASED_TO_SYSTEM_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];
                array_push($this->transactionInputs, $fromOrderToTransaction, $toTransactionFromOrder, $fromTransactionToWallet, $toWalletFromTransaction, $fromTransactionToSystem, $toSystemFromTransaction);

                if (bccomp($stockExchange->referral_earning, "0")) {
                    $fromTransactionToReferralEarning = [
                        'user_id' => $stockExchange->user_id,
                        'stock_item_id' => $this->stockPair->stock_item_id,
                        'model_name' => get_class($stockExchange),
                        'model_id' => $stockExchange->id,
                        'transaction_type' => TRANSACTION_TYPE_DEBIT,
                        'amount' => bcmul($stockExchange->referral_earning, '-1'),
                        'journal' => DECREASED_FROM_EXCHANGE_REFERRAL_EARNING_ON_SUCCESSFUL_TRANSACTION,
                        'created_at' => $this->date,
                        'updated_at' => $this->date
                    ];

                    $toReferralEarningFromTransaction = [
                        'user_id' => $stockExchange->user_id,
                        'stock_item_id' => $this->stockPair->stock_item_id,
                        'model_name' => get_class(new Wallet()),
                        'model_id' => null,
                        'transaction_type' => TRANSACTION_TYPE_CREDIT,
                        'amount' => $stockExchange->referral_earning,
                        'journal' => INCREASED_TO_WALLET_AS_REFERRAL_EARNING_ON_SUCCESSFUL_TRANSACTION,
                        'created_at' => $this->date,
                        'updated_at' => $this->date
                    ];

                    array_push($this->transactionInputs, $fromTransactionToReferralEarning, $toReferralEarningFromTransaction);
                }


            } else {

                $fromOrderToTransaction = [
                    'user_id' => $stockExchange->user_id,
                    'stock_item_id' => $this->stockPair->stock_item_id,
                    'model_name' => get_class($this->stockOrder),
                    'model_id' => $stockExchange->stock_order_id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($stockExchange->amount, '-1'),
                    'journal' => DECREASED_FROM_ORDER_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toTransactionFromOrder = [
                    'user_id' => $this->sellStockOrder->user_id,
                    'stock_item_id' => $this->stockPair->stock_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $stockExchange->amount,
                    'journal' => INCREASED_TO_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $fromTransactionToWallet = [
                    'user_id' => $this->sellStockOrder->user_id,
                    'stock_item_id' => $this->stockPair->base_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul(bcsub($stockExchange->total, $stockExchange->fee), '-1'),
                    'journal' => DECREASED_FROM_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toWalletFromTransaction = [
                    'user_id' => $this->sellStockOrder->user_id,
                    'stock_item_id' => $this->stockPair->base_item_id,
                    'model_name' => get_class(new Wallet()),
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => bcsub($stockExchange->total, $stockExchange->fee),
                    'journal' => INCREASED_TO_WALLET_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $fromTransactionToSystem = [
                    'user_id' => $this->sellStockOrder->user_id,
                    'stock_item_id' => $this->stockPair->base_item_id,
                    'model_name' => get_class($stockExchange),
                    'model_id' => $stockExchange->id,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($stockExchange->fee, '-1'),
                    'journal' => DECREASED_FROM_EXCHANGE_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                $toSystemFromTransaction = [
                    'user_id' => $this->sellStockOrder->user_id,
                    'stock_item_id' => $this->stockPair->base_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => $stockExchange->fee,
                    'journal' => INCREASED_TO_SYSTEM_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                    'created_at' => $this->date,
                    'updated_at' => $this->date
                ];

                array_push($this->transactionInputs, $fromOrderToTransaction, $toTransactionFromOrder, $fromTransactionToWallet, $toWalletFromTransaction, $fromTransactionToSystem, $toSystemFromTransaction);

                if (bccomp($stockExchange->referral_earning, "0")) {
                    $fromTransactionToReferralEarning = [
                        'user_id' => $this->sellStockOrder->user_id,
                        'stock_item_id' => $this->stockPair->base_item_id,
                        'model_name' => get_class($stockExchange),
                        'model_id' => $stockExchange->id,
                        'transaction_type' => TRANSACTION_TYPE_DEBIT,
                        'amount' => bcmul($stockExchange->referral_earning, '-1'),
                        'journal' => DECREASED_FROM_EXCHANGE_REFERRAL_EARNING_ON_SUCCESSFUL_TRANSACTION,
                        'created_at' => $this->date,
                        'updated_at' => $this->date
                    ];

                    $toReferralEarningFromTransaction = [
                        'user_id' => $this->sellStockOrder->user_id,
                        'stock_item_id' => $this->stockPair->base_item_id,
                        'model_name' => get_class(new Wallet()),
                        'model_id' => null,
                        'transaction_type' => TRANSACTION_TYPE_CREDIT,
                        'amount' => $stockExchange->referral_earning,
                        'journal' => INCREASED_TO_WALLET_AS_REFERRAL_EARNING_ON_SUCCESSFUL_TRANSACTION,
                        'created_at' => $this->date,
                        'updated_at' => $this->date
                    ];

                    array_push($this->transactionInputs, $fromTransactionToReferralEarning, $toReferralEarningFromTransaction);
                }

            }

            //Making exchanged orders broadcast payload
            $this->exchangedOrders[$stockExchange->exchange_type][] = [
                'price' => $stockExchange->price,
                'amount' => bcmul($stockExchange->amount, '-1'),
                'total' => bcmul($stockExchange->total, '-1'),
                'is_maker' => $stockExchange->is_maker,
                'exchange_type' => $stockExchange->exchange_type,
                'date' => $stockExchange->created_at->toDateTimeString()
            ];

            //Making private exchange orders broadcast payload
            $this->privateExchangedOrders[$stockExchange->user_id][] = [
                'order_number' => $stockExchange->stock_order_id,
                'exchange_type' => $stockExchange->exchange_type,
                'price' => $stockExchange->price,
                'amount' => $stockExchange->amount,
                'total' => $stockExchange->total,
                'fee' => bcadd($stockExchange->fee, $stockExchange->referral_earning),
                'is_maker' => $stockExchange->is_maker,
                'date' => $this->date->toDateTimeString()
            ];

        }
    }

    private function summaryReportDevelopment($stockExchange)
    {
        if ($stockExchange->exchange_type == EXCHANGE_BUY) {
            $this->stockPairSummary['base_item_buy_order_volume'] = bcadd($this->stockPairSummary['base_item_buy_order_volume'], $stockExchange->total);
            $this->stockPairSummary['stock_item_buy_order_volume'] = bcadd($this->stockPairSummary['stock_item_buy_order_volume'], $stockExchange->amount);

            $this->stockPairSummary['exchanged_buy_total'] = bcadd($this->stockPairSummary['exchanged_buy_total'], $stockExchange->total);
            $this->stockPairSummary['exchanged_buy_fee'] = bcadd($this->stockPairSummary['exchanged_buy_fee'], $stockExchange->fee);
        } elseif ($stockExchange->exchange_type == EXCHANGE_SELL) {
            $this->stockPairSummary['base_item_sale_order_volume'] = bcadd($this->stockPairSummary['base_item_sale_order_volume'], $stockExchange->total);
            $this->stockPairSummary['stock_item_sale_order_volume'] = bcadd($this->stockPairSummary['stock_item_sale_order_volume'], $stockExchange->amount);

            $this->stockPairSummary['exchanged_sale_total'] = bcadd($stockExchange->total, $this->stockPairSummary['exchanged_sale_total']);
            $this->stockPairSummary['exchanged_sale_fee'] = bcadd($this->stockPairSummary['exchanged_sale_fee'], $stockExchange->fee);
        }

        if ($stockExchange->is_maker) {
            $this->stockPairSummary['exchanged_amount'] = bcadd($stockExchange->amount, $this->stockPairSummary['exchanged_amount']);
            $this->stockPairSummary['exchanged_maker_total'] = bcadd($stockExchange->total, $this->stockPairSummary['exchanged_maker_total']);
        }
    }

    public function updateCoinPair()
    {
        $currentTime = $this->date->timestamp;
        $previousTime = $this->date->copy()->subDay()->timestamp;
        $exchange24 = json_decode($this->stockPair->exchange_24, true);

        if (!empty($exchange24)) {
            foreach ($exchange24 as $key => $value) {
                if ($key < $previousTime) {
                    unset($exchange24[$key]);
                } else {
                    break;
                }
            }
        }

        $exchange24[$currentTime] = [
            'price' => $this->stockPairSummary['last_price'],
            'amount' => $this->stockPairSummary['exchanged_amount'],
            'total' => $this->stockPairSummary['exchanged_maker_total']
        ];

        $pairUpdate = app(StockPairInterface::class)->update([
            'base_item_buy_order_volume' => DB::raw('base_item_buy_order_volume - ' . $this->stockPairSummary['base_item_buy_order_volume']),
            'stock_item_buy_order_volume' => DB::raw('stock_item_buy_order_volume - ' . $this->stockPairSummary['stock_item_buy_order_volume']),
            'base_item_sale_order_volume' => DB::raw('base_item_sale_order_volume - ' . $this->stockPairSummary['base_item_sale_order_volume']),
            'stock_item_sale_order_volume' => DB::raw('stock_item_sale_order_volume - ' . $this->stockPairSummary['stock_item_sale_order_volume']),

            'exchanged_buy_total' => DB::raw('exchanged_buy_total + ' . $this->stockPairSummary['exchanged_buy_total']),
            'exchanged_sale_total' => DB::raw('exchanged_sale_total + ' . $this->stockPairSummary['exchanged_sale_total']),

            'exchanged_maker_total' => DB::raw('exchanged_maker_total + ' . $this->stockPairSummary['exchanged_maker_total']),
            'exchanged_amount' => DB::raw('exchanged_amount + ' . $this->stockPairSummary['exchanged_amount']),

            'exchanged_buy_fee' => DB::raw('exchanged_buy_fee + ' . $this->stockPairSummary['exchanged_buy_fee']),
            'exchanged_sale_fee' => DB::raw('exchanged_sale_fee + ' . $this->stockPairSummary['exchanged_sale_fee']),

            'last_price' => $this->stockPairSummary['last_price'],
            'exchange_24' => json_encode($exchange24)
        ], $this->stockPair->id);

        if (!$pairUpdate) {
            return false;
        }


        $price = array_column($exchange24, 'price');
        $max = max($price);
        $min = min($price);
        $first = array_first($price);
        $last = array_last($price);

        $this->stockPairSummary = [
            'stock_pair_id' => $this->stockPair->id,
            'last_price' => $this->exchangePrice,
            'exchanged_stock_item_volume_24' => array_sum(array_column($exchange24, 'amount')),
            'exchanged_base_item_volume_24' => array_sum(array_column($exchange24, 'total')),
            'high_24' => $max,
            'low_24' => $min,
            'change_24' => empty($first) ? 0 : bcmul(bcdiv(bcsub($last, $first), $first), '100', 2)
        ];

        return true;
    }

    private function getTimeIntervals($date)
    {
        $data = [];
        $timeOffset = date_offset_get($date);
        $unixTime = strtotime($date) + $timeOffset;
        $intervals = [5, 15, 30, 120, 240, 1440];
        foreach ($intervals as $interval) {
            $data[$interval] = date('Y-m-d H:i:s', (($unixTime - ($unixTime % ($interval * 60))) - $timeOffset));
        }

        return $data;
    }


}