<?php

namespace Redbeed\OpenOverlay\Listeners;

use Redbeed\OpenOverlay\Events\Twitch\EventReceived;
use Redbeed\OpenOverlay\Events\Twitch\StreamOffline;
use Redbeed\OpenOverlay\Events\Twitch\StreamOnline;

class TwitchSplitReceivedEvents
{
    public function handle(EventReceived $twitchEvent)
    {
        if ($twitchEvent->event->event_type === 'stream.online') {
            broadcast(new StreamOnline($twitchEvent->event));

            return;
        }

        if ($twitchEvent->event->event_type === 'stream.offline') {
            broadcast(new StreamOffline($twitchEvent->event));

            return;
        }
    }
}
