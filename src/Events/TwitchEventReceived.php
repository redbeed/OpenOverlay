<?php

namespace Redbeed\OpenOverlay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;

class TwitchEventReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var EventSubEvents */
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function broadcastOn(): PresenceChannel
    {
        return new PresenceChannel('twitch.'.$this->event->event_user_id.'.event');
    }

    public function broadcastAs(): string
    {
        return 'twitch.event.send';
    }
}
