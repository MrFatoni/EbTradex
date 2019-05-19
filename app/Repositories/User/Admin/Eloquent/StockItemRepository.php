<?php

namespace App\Repositories\User\Admin\Eloquent;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Models\Backend\StockItem;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class StockItemRepository extends BaseRepository implements StockItemInterface
{
    protected $model;

    public function __construct(StockItem $model)
    {
        $this->model = $model;
    }

    public function getActiveList($stockItemType = null) {
        $conditions = ['is_active' => ACTIVE_STATUS_ACTIVE];

        if( !is_null($stockItemType) ) {
            $conditions['item_type'] = $stockItemType;
        }

        return $this->model->select('id', DB::raw("CONCAT(item, ' (', item_name,')') AS item"))->where($conditions)->get();
    }

    public function getCountByConditions(array $conditions)
    {
        return $this->model->where($conditions)->count();
    }

    public function getStockPairsById($id)
    {
        return $this->model->where('id', $id)
            ->leftJoin('stock_pairs as base', 'base.base_item_id', '=', 'stock_items.id')
            ->leftJoin('stock_pairs as stock', 'stock.stock_item_id', '=', 'stock_items.id')
            ->select([
                // stock pair id
                'stock_items.*',
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

            ]);
    }
}