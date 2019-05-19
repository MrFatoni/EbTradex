<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class Navigation extends Model implements AuditableInterface
{
    use Auditable;

    protected $fillable = ['slug', 'navigation_items'];

    protected $fakeFields = ['slug', 'navigation_items'];

    public function getNavigationItemsAttribute($value){
        return json_decode($value, true);
    }

    public function setNavigationItemsAttribute($value){
        return $this->attributes['navigation_items'] =json_encode($value);
    }
}
