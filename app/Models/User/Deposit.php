<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = ['user_id', 'ref_id', 'wallet_id', 'stock_item_id', 'amount', 'network_fee', 'system_fee', 'address', 'txn_id', 'payment_method', 'status'];
}
