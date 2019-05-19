<?php

namespace App\Repositories\User\Admin\Eloquent;

use App\Models\Backend\StockPair;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class StockPairRepository extends BaseRepository implements StockPairInterface
{
    protected $model;

    public function __construct(StockPair $model)
    {
        $this->model = $model;
    }

    public function updateRows(array $attributes, array $conditions = null)
    {
        $model = is_null($conditions) ? $this->model : $this->model->where($conditions);

        return $model->update($attributes);
    }

    public function getFirstStockPairDetailByConditions($conditions)
    {
        $stockPair = $this->getStockPair($conditions)->first();
        $date = Carbon::now()->subDay()->timestamp;

        $this->generateExchangeSummary($stockPair, $date);

        return $stockPair;
    }

    public function getStockPair($conditions)
    {
        return $this->model->where($conditions)
            ->leftJoin('stock_items as base_item', 'base_item.id', '=', 'stock_pairs.base_item_id')
            ->leftJoin('stock_items as stock_item', 'stock_item.id', '=', 'stock_pairs.stock_item_id')
            ->select([
                // stock pair id
                'stock_pairs.id as id',
                // stock item
                'stock_item.id as stock_item_id',
                'stock_item.item as stock_item_abbr',
                'stock_item.item_name as stock_item_name',
                'stock_item.item_type as stock_item_type',
                // base item
                'base_item.id as base_item_id',
                'base_item.item as base_item_abbr',
                'base_item.item_name as base_item_name',
                'base_item.item_type as base_item_type',
                // 24hr pair detail
                'last_price',
                'exchange_24',
                //summary
                'stock_pairs.base_item_buy_order_volume',
                'stock_pairs.stock_item_buy_order_volume',
                'stock_pairs.base_item_sale_order_volume',
                'stock_pairs.stock_item_sale_order_volume',
                'stock_pairs.exchanged_buy_total',
                'stock_pairs.exchanged_sale_total',
                'stock_pairs.exchanged_amount',
                'stock_pairs.exchanged_maker_total',
                'stock_pairs.exchanged_buy_fee',
                'stock_pairs.exchanged_sale_fee',
                'stock_pairs.is_active',
                'stock_pairs.is_default',
                'stock_pairs.created_at',
            ]);
    }

    private function generateExchangeSummary(& $stockPair, $date)
    {
        $exchange24 = json_decode($stockPair->exchange_24, true);

        $stockPair->exchanged_stock_item_volume_24 = 0;
        $stockPair->exchanged_base_item_volume_24 = 0;
        $stockPair->high_24 = 0;
        $stockPair->low_24 = 0;
        $stockPair->change_24 = 0;

        if (!empty($exchange24)) {

            foreach ($exchange24 as $time => $data) {
                if ($date > $time) {
                    unset($exchange24[$time]);
                } else {
                    break;
                }
            }

            if (!empty($exchange24)) {
                $firstPrice = array_first($exchange24)['price'];
                $lastPrice = array_last($exchange24)['price'];
                $stockPair->exchanged_stock_item_volume_24 = array_sum(array_column($exchange24, 'amount'));
                $stockPair->exchanged_base_item_volume_24 = array_sum(array_column($exchange24, 'total'));
                $stockPair->high_24 = max(array_column($exchange24, 'price'));
                $stockPair->low_24 = min(array_column($exchange24, 'price'));
                $stockPair->change_24 = bcmul(bcdiv(bcsub($lastPrice, $firstPrice), $firstPrice), '100');
            }
        }
    }

    public function getAllStockPairDetailByConditions($conditions)
    {
        $stockPairs = $this->getStockPair($conditions)->get();
        $date = Carbon::now()->subDay()->timestamp;

        foreach ($stockPairs as $stockPair) {
            $this->generateExchangeSummary($stockPair, $date);
        }

        return $stockPairs;
    }

    function getByPair($stockItem, $baseItem)
    {
        $select = ['stock_pairs.*'];
        $where = [
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'si_stock.item' => $stockItem,
            'si_stock.is_active' => ACTIVE_STATUS_ACTIVE,
            'si_base.item' => $baseItem,
            'si_base.is_active' => ACTIVE_STATUS_ACTIVE,
        ];
        return $this->model->select($select)->where($where)->leftJoin('stock_items as si_stock', 'si_stock.id', '=', 'stock_pairs.stock_item_id')->leftJoin('stock_items as si_base', 'si_base.id', '=', 'stock_pairs.base_item_id')->first();
    }

    function getAllStockPairForApiByConditions($conditions)
    {
        $stockPairs = $this->getStockPair($conditions)->get();

        $date = Carbon::now()->subDay()->timestamp;

        $stockPairDetails = [];

        foreach ($stockPairs as $stockPair) {
            $this->generateExchangeSummary($stockPair, $date);
            $stockPairDetails[$stockPair->stock_item_abbr . '_' . $stockPair->base_item_abbr] = [
                'last'	=> $stockPair->last_price,
                'percentChange' =>	$stockPair->change_24,
                'baseVolume' =>	$stockPair->exchanged_base_item_volume_24,
                'coinVolume' => $stockPair->exchanged_stock_item_volume_24,
                'high24hr' => $stockPair->high_24,
                'low24hr' => $stockPair->low_24,
            ];

        }

        return $stockPairDetails;
    }
}