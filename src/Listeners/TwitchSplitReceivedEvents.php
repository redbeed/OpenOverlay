<?php

namespace Redbeed\OpenOverlay\Listeners;

use Redbeed\OpenOverlay\Events\TwitchEventReceived;
use Redbeed\OpenOverlay\Events\TwitchStreamOffline;
use Redbeed\OpenOverlay\Events\TwitchStreamOnline;

class TwitchSplitReceivedEvents
{
    public function handle(TwitchEventReceived $twitchEvent)
    {
        if ($twitchEvent->event->event_type === 'stream.online') {
            broadcast(new TwitchStreamOnline($twitchEvent->event));
            return;
        }

        if ($twitchEvent->event->event_type === 'stream.offline') {
            broadcast(new TwitchStreamOffline($twitchEvent->event));
            return;
        }
    }
}
