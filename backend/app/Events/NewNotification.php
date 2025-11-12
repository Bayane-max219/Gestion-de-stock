<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        if ($this->notification->is_broadcast) {
            // For store-wide notifications, broadcast to store channel
            if ($this->notification->store_id) {
                return new PrivateChannel('store.' . $this->notification->store_id);
            }

            // For system-wide notifications
            return new Channel('notifications');
        }

        // For user-specific notifications
        if ($this->notification->notifiable_type === 'App\Models\User') {
            return new PrivateChannel('user.' . $this->notification->notifiable_id);
        }

        return null;
    }

    public function broadcastAs()
    {
        return 'new.notification';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'category' => $this->notification->category,
            'priority' => $this->notification->priority,
            'data' => $this->notification->data,
            'created_at' => $this->notification->created_at,
        ];
    }
}