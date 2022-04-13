<?php

namespace Redbeed\OpenOverlay\Automations\Triggers;

use Illuminate\Support\Str;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;

class TwitchChatMessageTrigger extends Trigger
{
    static protected string $name = 'Twitch Chat Message';

    static protected string $description = 'Trigger when Twitch chat message is received';

    public ChatMessage $message;

    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    public function valid(): bool
    {
        if (empty($this->options['message'])) {
            return true;
        }

        return Str::contains($this->message->message, $this->options['message']);
    }
}
