<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'commentable_id', 'content', 'commentable_type'];

    protected $fakeFields = ['commentable_id', 'content', 'commentable_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function commentable()
    {
        return $this->morphTo();
    }
}
