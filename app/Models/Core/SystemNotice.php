<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class SystemNotice extends Model implements AuditableInterface
{
    use Auditable;

    protected $fillable = ['title', 'description', 'type', 'start_at', 'end_at', 'status'];

    protected $fakeFields = ['title', 'description', 'type', 'start_at', 'end_at', 'status'];
}
