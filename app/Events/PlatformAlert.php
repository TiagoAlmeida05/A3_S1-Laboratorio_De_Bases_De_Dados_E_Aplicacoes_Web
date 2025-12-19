<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlatformAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;
    public $notificationId;
    public $typeId;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $userId, $notificationId, $typeId)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->notificationId = $notificationId;
        $this->typeId = $typeId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.'. $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PlatformAlert';
    }

}
