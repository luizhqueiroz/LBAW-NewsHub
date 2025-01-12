<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['sender_id', 'news_id', 'comment_id'];
    public $timestamps = false;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
