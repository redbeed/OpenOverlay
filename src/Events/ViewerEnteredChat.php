<?php

namespace Redbeed\OpenOverlay\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Redbeed\OpenOverlay\Models\User\Connection;

class ViewerEnteredChat implements ShouldBroadcastNow
{
    /** @var Connection */
    public $streamer;

    /** @var string */
    public $username;

    public function __construct(string $username, Connection $streamer)
    {
        $this->streamer = $streamer;
        $this->username = $username;
    }

    public function broadcastAs(): string
    {
        return 'viewer-chat-entered';
    }

    public function broadcastWith()
    {
        return [
            'username' => $this->username,
        ];
    }

    public function broadcastOn()
    {
        return new Channel('twitch.' . $this->streamer->service_user_id);
    }
}
