<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'notification_date', 'viewed', 'notification_type'];
    const CREATED_AT = 'notification_date';
    const UPDATED_AT = null;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function commentNotification()
    {
        return $this->hasOne(NotificationComment::class, 'notification_id');
    }

    public function likeNotification()
    {
        return $this->hasOne(NotificationLike::class, 'notification_id');
    }

    public function like()
    {
        return $this->hasOneThrough(
            Like::class,         
            NotificationLike::class,
            'notification_id',   
            'id',                
            'id',               
            'like_id'           
        );
    }

    public function comment()
    {
        return $this->hasOneThrough(
            Comment::class,         
            NotificationComment::class,
            'notification_id',   
            'id',                
            'id',               
            'comment_id'           
        );
    }
}
