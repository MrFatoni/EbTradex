<?php

namespace App\Models\Backend;

use App\Models\User\StockOrder;
use Illuminate\Database\Eloquent\Model;

class StockExchange extends Model
{
    protected $fillable = [
        'user_id',
        'stock_exchange_group_id',
        'stock_order_id',
        'stock_pair_id',
        'amount',
        'price',
        'total',
        'fee',
        'referral_earning',
        'exchange_type',
        'related_order_id',
        'base_order',
        'is_maker',
    ];

    public function stockOrder()
    {
        return $this->belongsTo(StockOrder::class);
    }
}
