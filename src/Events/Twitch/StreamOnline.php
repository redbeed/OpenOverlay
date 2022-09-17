<?php

namespace Redbeed\OpenOverlay\Events\Twitch;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Support\StreamerOnline;

class StreamOnline implements ShouldBroadcastNow
{
    /** @var EventSubEvents */
    private $twitchEvent;

    /** @var Connection */
    public $twitchUser;

    public function __construct(EventSubEvents $twitchEvent)
    {
        $this->twitchEvent = $twitchEvent;
        $this->twitchUser = Connection::where('service_username', $this->twitchEvent->event_user_id)->first();

        StreamerOnline::setOnline(
            $this->twitchEvent->event_user_id,
            $this->twitchEvent->event_data['started_at']
        );

        event(new RefresherEvent($this->twitchUser));
    }

    public function broadcastOn(): Channel
    {
        return new Channel('twitch.'.$this->twitchUser->service_user_id);
    }

    public function broadcastAs(): string
    {
        return 'stream-online';
    }

    public function broadcastWith()
    {
        return [
            'started' => StreamerOnline::onlineTime($this->twitchEvent->event_user_id),
        ];
    }
}
