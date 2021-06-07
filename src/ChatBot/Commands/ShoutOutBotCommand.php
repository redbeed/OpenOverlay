<?php

namespace Redbeed\OpenOverlay\ChatBot\Commands;

use GuzzleHttp\Exception\ClientException;
use Redbeed\OpenOverlay\ChatBot\Twitch\ChatMessage;
use Redbeed\OpenOverlay\Service\Twitch\ChannelsClient;
use Redbeed\OpenOverlay\Service\Twitch\UsersClient;

class ShoutOutBotCommand extends BotCommand
{
    public $signature = '!so {username}';

    public function response(ChatMessage $chatMessage): string
    {
        $username = ltrim($this->parameter('username'), '@');

        $usersClient = new UsersClient();
        try {
            $users = $usersClient->byUsername($username);
        } catch (ClientException $exception) {
            return '';
        }

        $user = head($users['data']);
        if ($user['login'] !== strtolower($username)) {
            return '';
        }

        $response = [
            'Don´t forget to checkout ' . $user['display_name'] . ' www.twitch.tv/' . $user['login']
        ];

        try {
            $channelClient = new ChannelsClient();
            $channels = $channelClient->get($user['id']);
            $channel = head($channels['data']);

            if(!empty($channel['game_id'])) {
                $response[] = '- currently playing "'.$channel['game_name'].'"';
            }
        } catch (ClientException $exception) {
            // ignore
        }

        return implode(' ', $response);
    }
}
