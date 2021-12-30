<?php

namespace Redbeed\OpenOverlay\Console\Scheduling;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\SendMessageCommand;
use Redbeed\OpenOverlay\Models\BotConnection;

class ChatBotScheduling extends Schedule
{
    /** @var BotConnection */
    protected $bot;

    protected $user;

    protected function valid($user): bool
    {
        if (empty($this->message())) {
            return false;
        }

        return true;
    }

    protected function message(): string
    {
        return '';
    }

    protected function schedule(Event $event): Event
    {
        return $event->everyFiveMinutes();
    }

    public function getJob(Schedule $schedule, $user): ?Event
    {
        if (!$this->valid($user)) {
            return null;
        }

        return $this->schedule($schedule->command(SendMessageCommand::class, [$user->id, $this->message()]));
    }

}
