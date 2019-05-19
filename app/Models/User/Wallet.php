<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['id', 'user_id', 'stock_item_id', 'primary_balance', 'on_order_balance', 'address', 'is_active'];

    public function stockItem(){
        return $this->belongsTo(StockItem::class);
    }
}
