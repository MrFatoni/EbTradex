<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;

class ReferralEarning extends Model
{
    protected $guarded = ['id'];

    public function referrerUser()
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referralUser()
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }


}
