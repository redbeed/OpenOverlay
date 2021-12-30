<?php

namespace Redbeed\OpenOverlay\Events\Twitch;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Redbeed\OpenOverlay\Models\Twitch\EventSubEvents;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Support\StreamerOnline;
use Redbeed\OpenOverlay\Support\ViewerInChat;

class StreamOffline implements ShouldBroadcastNow
{
    /** @var EventSubEvents */
    private $twitchEvent;

    /** @var Connection */
    public $twitchUser;

    /** @var Carbon|null */
    private $streamStarted;

    public function __construct(EventSubEvents $twitchEvent)
    {
        $this->twitchEvent = $twitchEvent;
        $this->twitchUser = Connection::where('service_username', $this->twitchEvent->event_user_id)->first();
        $this->streamStarted = StreamerOnline::onlineTime($this->twitchEvent->event_user_id);

        ViewerInChat::clear($this->twitchUser);
        StreamerOnline::setOffline($this->twitchEvent->event_user_id);

        event(new RefresherEvent($this->twitchUser));
    }

    public function broadcastOn(): Channel
    {
        return new Channel('twitch.' . $this->twitchUser->service_user_id);
    }

    public function broadcastAs(): string
    {
        return 'stream-offline';
    }

    public function broadcastWith()
    {
        return [
            'started' => $this->streamStarted,
            'ended'   => Carbon::now(),
        ];
    }
}
