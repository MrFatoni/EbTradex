<?php

namespace App\Models\Backend;

use App\Models\User\Comment;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'content', 'featured_image', 'is_published'];

    protected $fakeFields = ['title', 'content', 'featured_image', 'is_published'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}