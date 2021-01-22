<?php

namespace Redbeed\OpenOverlay\ChatBot\Commands;

use Carbon\Carbon;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;

class HelloWorldBotCommand extends BotCommand
{
    public $signature = '!hello-advance';

    public function response(ChatMessage $chatMessage): string
    {
        return implode(' ', [
            'Hello World.. I mean ' . $chatMessage->username,
            'It is ' . Carbon::now()->toString() . '... I think',
        ]);
    }
}
