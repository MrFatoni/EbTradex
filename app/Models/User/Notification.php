<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class Notification extends Model implements AuditableInterface
{
    use Auditable;

    protected $fillable = ['user_id', 'data', 'read_at'];

    protected $fakeFields = ['user_id', 'data', 'read_at'];
}
