<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'stock_item_id', 'table_name', 'row_id', 'transaction_type', 'amount'];
}