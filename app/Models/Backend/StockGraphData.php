<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class StockGraphData extends Model
{
    protected $fillable = ['stock_pair_id', '5min', '15min', '30min', '2hr', '4hr', '1day','created_at','updated_at'];
}
