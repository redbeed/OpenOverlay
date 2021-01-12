<?php

namespace Redbeed\OpenOverlay\ChatBot\Commands;

use Carbon\Carbon;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;

class  HelloWorldBotCommand extends BotCommand
{
    public $command = '!hello-advance';

    public function response(ChatMessage $chatMessage): string
    {
        return implode(' ', [
            'Hello World.. i mean ' . $chatMessage->username,
            'It is ' . Carbon::now()->toString() . '... i think',
        ]);
    }
}
