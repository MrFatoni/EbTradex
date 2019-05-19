<?php

namespace App\Models\User;

use App\Models\Backend\StockPair;
use Illuminate\Database\Eloquent\Model;

class StockOrder extends Model
{
    protected $fillable = [
        'user_id',
        'stock_pair_id',
        'category',
        'exchange_type',
        'status',
        'price',
        'amount',
        'exchanged',
        'canceled',
        'stop_limit',
        'maker_fee',
        'taker_fee',
    ];

    protected $fakeFields = [
        'user_id',
        'stock_pair_id',
        'category',
        'exchange_type',
        'status',
        'price',
        'amount',
        'exchanged',
        'canceled',
        'stop_limit',
        'maker_fee',
        'taker_fee',
    ];

    public function stockPair()
    {
        return $this->belongsTo(StockPair::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
