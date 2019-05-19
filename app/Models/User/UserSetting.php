<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class UserSetting extends Model implements AuditableInterface
{
    use Auditable;

    protected $fillable = ['user_id', 'language', 'timezone'];

    protected $fakeFields = ['language', 'timezone', 'is_otp_allowed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
