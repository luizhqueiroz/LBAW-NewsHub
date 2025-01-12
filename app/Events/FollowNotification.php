<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FollowNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $follower;
    public $followed;

    /**
     * Create a new event instance.
     */
    public function __construct($follower, $followed)
    {
        $this->follower = $follower;
        $this->followed = $followed;
    }

    public function broadcastOn()
    {
        return ['user.' . $this->followed->id];
    }

    public function broadcastAs()
    {
        return 'Notification-follow';
    }
}
