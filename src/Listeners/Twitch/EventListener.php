<?php

namespace Redbeed\OpenOverlay\Listeners\Twitch;

use Illuminate\Contracts\Queue\ShouldQueue;
use Redbeed\OpenOverlay\Events\Twitch\EventReceived;

abstract class EventListener implements ShouldQueue
{

    protected function eventType(): string
    {
        return 'event.type'; // https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types
    }

    public function eventValid(EventReceived $event): bool
    {
        return $event->event->event_type === $this->eventType();
    }

    public function handle(EventReceived $event)
    {
        if ($this->eventValid($event) === false) {
            return;
        }

        $this->handleEvent($event);
    }


    abstract public function handleEvent(EventReceived $event): void;
}
