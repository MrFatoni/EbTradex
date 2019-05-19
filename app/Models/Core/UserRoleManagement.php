<?php

namespace App\Models\Core;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class UserRoleManagement extends Model implements AuditableInterface
{
    use Auditable;

    protected $fillable = ['role_name', 'route_group'];

    protected $fakeFields = ['role_name', 'route_group'];

    public function getRouteGroupAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setRouteGroupAttribute($value)
    {
        $this->attributes['route_group'] = json_encode($value);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
