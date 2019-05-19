<?php

namespace App\Models\User;

use App\Models\Core\UserRoleManagement;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class User extends Authenticatable implements AuditableInterface
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'email', 'user_role_management_id', 'remember_me', 'avatar', 'is_email_verified', 'is_financial_active', 'is_accessible_under_maintenance', 'google2fa_secret', 'is_active', 'created_by_admin','referral_code','referrer_id'];

    protected $fakeFields = ['username', 'password', 'email', 'user_role_management_id', 'remember_me', 'avatar', 'is_email_verified', 'is_financial_active', 'is_accessible_under_maintenance', 'google2fa_secret', 'is_active', 'created_by_admin'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function userRoleManagement()
    {
        return $this->belongsTo(UserRoleManagement::class);
    }

    public function userInfo()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function userSetting()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }
}
