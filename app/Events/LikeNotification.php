<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LikeNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $like;

    /**
     * Create a new event instance.
     */
    public function __construct($like)
    {
        $this->like = $like;
    }

    public function broadcastOn()
    {   
        if ($this->like->comment_id) {
            return ['news.' . $this->like->comment->news_id];
        } elseif ($this->like->news_id) {
            return ['news.' . $this->like->news_id];
        }
    }

    public function broadcastAs()
    {
        return 'Notification-like';
    }
    
}
