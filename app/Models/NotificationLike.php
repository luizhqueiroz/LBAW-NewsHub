<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLike extends Model
{
    protected $table = 'notifications_like';
    protected $fillable = ['notification_id', 'like_id'];
    public $timestamps = false;

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function like()
    {
        return $this->belongsTo(Like::class, 'like_id');
    }
}
