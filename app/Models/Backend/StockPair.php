<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class StockPair extends Model
{

    protected $fillable = ['stock_item_id', 'base_item_id', 'is_active', 'is_default', 'base_item_buy_order_volume', 'stock_item_buy_order_volume', 'base_item_sale_order_volume', 'stock_item_sale_order_volume', 'exchanged_buy_total', 'exchanged_sale_total', 'exchanged_maker_total', 'exchanged_amount', 'exchanged_buy_fee', 'exchanged_sale_fee', 'last_price', 'exchange_24', 'ico_total_earned', 'ico_fee_earned','ico_total_sold'];

    protected $fakeFields = ['stock_item_id', 'base_item_id', 'is_active', 'is_default', 'base_item_buy_order_volume', 'stock_item_buy_order_volume', 'base_item_sale_order_volume', 'stock_item_sale_order_volume', 'exchanged_buy_total', 'exchanged_sale_total', 'exchanged_maker_total', 'exchanged_amount', 'exchanged_buy_fee', 'exchanged_sale_fee', 'last_price', 'exchange_24', 'ico_total_earned', 'ico_fee_earned','ico_total_sold'];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id', 'id');
    }

    public function baseItem()
    {
        return $this->belongsTo(StockItem::class, 'base_item_id', 'id');
    }

    public function getStockPairAttribute()
    {
        return $this->stockItem->item . '/' . $this->baseItem->item;
    }
}