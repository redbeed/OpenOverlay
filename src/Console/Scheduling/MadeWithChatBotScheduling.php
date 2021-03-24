<?php

namespace Redbeed\OpenOverlay\Console\Scheduling;

use Illuminate\Console\Scheduling\Event;

class MadeWithChatBotScheduling extends ChatBotScheduling
{
    protected function message(): string
    {
        return 'I\'m made with OpenOverlay.dev';
    }

    protected function schedule(Event $event): Event
    {
        return $event->everyFifteenMinutes();
    }
}
