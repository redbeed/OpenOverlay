<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class ChannelsClient extends ApiClient
{
    public function get(string $broadcasterId): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'broadcaster_id' => $broadcasterId,
                ],
            ])
            ->request('GET', 'channels');
    }

    public function lastGame(string $broadcasterId) {
        $channels = (new self)->get($broadcasterId);
        $channel = head($channels['data']);

        return $channel['game_name'];
    }
}
