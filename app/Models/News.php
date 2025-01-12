<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['content', 'published_date', 'author_id', 'image_id', 'movie_id'];
    const CREATED_AT = 'published_date';
    const UPDATED_AT = null;
    
    public function author() 
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function image() 
    {
        return $this->belongsTo(Image::class);
    }

    public function tags() 
    {
        return $this->belongsToMany(Tag::class, 'tag_news', 'news_id', 'tag_id');
    }

    public function comments() 
    {
        return $this->hasMany(Comment::class, 'news_id')->orderBy('published_date', 'desc');;
    }

    public function likes() 
    {
        return $this->hasMany(Like::class, 'news_id');
    }

    public function isLikedByUser($userId) 
    {
        return $this->likes()->where('sender_id', $userId)->exists();
    }

}
