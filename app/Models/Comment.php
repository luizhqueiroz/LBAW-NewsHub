<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';
    protected $fillable = ['content', 'published_date', 'news_id', 'author_id', 'img_id'];
    const CREATED_AT = 'published_date';
    const UPDATED_AT = null;

    public function news() 
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public function author() 
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function image() 
    {
        return $this->belongsTo(Image::class, 'img_id');
    }

    public function likes() 
    {
        return $this->hasMany(Like::class, 'comment_id');
    }

    public function isLikedByUser($userId) 
    {
        return $this->likes()->where('sender_id', $userId)->exists();
    }

}
