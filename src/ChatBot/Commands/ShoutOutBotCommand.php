<?php

namespace App\Bot\Commands;

use Redbeed\OpenOverlay\ChatBot\Commands\BotCommand;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;

class ShoutOutBotCommand extends BotCommand
{
    public $signature = '!so {username}';

    public function response(ChatMessage $chatMessage): string
    {
        $username = $this->parameter('username');

        return implode(' ', [
            'Don´t forget to checkout www.twitch.tv/'.$username,
        ]);
    }
}
