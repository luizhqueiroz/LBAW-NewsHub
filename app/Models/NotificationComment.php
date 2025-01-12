<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationComment extends Model
{
    protected $table = 'notifications_comment';
    protected $fillable = ['notification_id', 'comment_id'];
    public $timestamps = false;

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
