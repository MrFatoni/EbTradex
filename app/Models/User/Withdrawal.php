<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['user_id', 'ref_id', 'wallet_id', 'stock_item_id', 'amount', 'network_fee', 'system_fee', 'address', 'txn_id', 'payment_method', 'status'];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
