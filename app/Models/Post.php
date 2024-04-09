<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Post extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sharing()
    {
        return $this->belongsTo(Post::class, 'sharing_post_id', 'id')
            ->with('user')
            ->with("sharing")
            ->withCount("sharings")
            ->withCount("likes")
            ->withCount("comments")
            ->withExists("isLike");
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'post_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, "post_id", "id");
    }
    public function sharings()
    {
        return $this->hasMany(Post::class, 'sharing_post_id', 'id');
    }

    public function isLike()
    {
        return $this->likes()->where("owner_id", auth()->id());
    }

}
