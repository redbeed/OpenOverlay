<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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
