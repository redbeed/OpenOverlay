<?php

namespace Redbeed\OpenOverlay\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;

class TwitchEventReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var EventSubEvents */
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('twitch.'.$this->event->event_user_id);
    }

    public function broadcastAs(): string
    {
        return 'event-received';
    }

    public function broadcastWith()
    {
        return [
            'type' => $this->event->event_type,
            'data' => $this->event->event_data,
            'created_at' => $this->event->event_sent,
        ];
    }
}
