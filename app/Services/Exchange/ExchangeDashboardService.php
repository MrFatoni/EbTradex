<?php

namespace App\Services\Backend;

use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ExchangeDashboardService
{
    public function getDefaultStockPair($pair)
    {
        $stockPairRepository = app(StockPairInterface::class);
        if (empty($pair)) {
            $stockPairId = Cookie::get('stockPairID');

            if (empty($stockPairId)) {
                $stockPair = $stockPairRepository->getFirstByConditions(['is_active' => ACTIVE_STATUS_ACTIVE, 'is_default' => ACTIVE_STATUS_ACTIVE]);
                if (!empty($stockPair)) {
                    cookie()->forever('stockPairID', $stockPair->id);
                }
            } else {
                $stockPair = $stockPairRepository->getFirstById($stockPairId);
            }
        } else {
            $pair = explode('-', $pair);
            $stockItem = strtoupper($pair[0]);
            $baseItem = strtoupper($pair[1]);
            $stockPair = $stockPairRepository->getByPair($stockItem, $baseItem);

            if (!empty($stockPair)) {
                cookie()->forever('stockPairID', $stockPair->id);
            }
        }

        return $stockPair;
    }

    public function get24HrPairDetail($stockPairID)
    {
        $conditions = [
            'stock_pairs.id' => $stockPairID,
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'stock_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'base_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
            'stock_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
        ];

        $get24hrPairData = app(StockPairInterface::class)->getFirstStockPairDetailByConditions($conditions);

        if (empty($get24hrPairData)) {
            return false;
        }

        $pair24HrDetail = [
            'baseItem' => $get24hrPairData->base_item_abbr,
            'stockItem' => $get24hrPairData->stock_item_abbr,
            'lastPrice' => $get24hrPairData->last_price,
            'change24hrInPercent' => $get24hrPairData->change_24,
            'high24hr' => $get24hrPairData->high_24,
            'low24hr' => $get24hrPairData->low_24,
            'baseVolume' => $get24hrPairData->exchanged_base_item_volume_24,
            'stockVolume' => $get24hrPairData->exchanged_stock_item_volume_24
        ];

        return $pair24HrDetail;
    }

    public function getStockMarket()
    {
        $conditions = [
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'stock_item.is_ico' => ACTIVE_STATUS_INACTIVE,
            'base_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
            'stock_item.exchange_status' => ACTIVE_STATUS_ACTIVE,
        ];

        return app(StockPairInterface::class)->getAllStockPairDetailByConditions($conditions);
    }

    public function getOrders($stockPairID, $lastPrice = null, $exchangeType = EXCHANGE_SELL, $category = CATEGORY_EXCHANGE)
    {
        $conditions = [
            'stock_pair_id' => $stockPairID,
            'category' => $category,
            'exchange_type' => $exchangeType,
            'status' => STOCK_ORDER_PENDING
        ];

        if (!empty($lastPrice)) {
            if ($exchangeType == EXCHANGE_SELL) {
                array_push($conditions, ['price', '>', $lastPrice]);
            } else {
                array_push($conditions, ['price', '<', $lastPrice]);
            }
        }


        $stockOrders = app(StockOrderInterface::class)->getOrders($conditions);
        $totalStockOrders = app(StockOrderInterface::class)->getTotalStockOrder($conditions);

        return [
            'stockOrders' => $stockOrders->toArray(),
            'totalStockOrder' => $totalStockOrders
        ];
    }

    public function getWalletSummary($stockPairId)
    {
        $walletRepository = app(WalletInterface::class);

        $walletRepository->createUnavailableWallet(Auth::id());

        $stockPair = app(StockPairInterface::class)->getFirstById($stockPairId);

        $conditions = [
            'stock_item_id' => $stockPair->base_item_id,
            'user_id' => Auth::id()
        ];

        $baseItemWallet = $walletRepository->getFirstByConditions($conditions);

        $conditions['stock_item_id'] = $stockPair->stock_item_id;

        $stockItemWallet = $walletRepository->getFirstByConditions($conditions);

        return [
            'base_item_balance' => $baseItemWallet->primary_balance,
            'base_item_on_order' => $baseItemWallet->on_order_balance,
            'stock_item_balance' => $stockItemWallet->primary_balance,
            'stock_item_on_order' => $stockItemWallet->on_order_balance,
        ];
    }

}