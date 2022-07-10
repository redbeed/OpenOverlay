<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;
use Redbeed\OpenOverlay\Exceptions\TwitchEmoteSetIdException;
use Redbeed\OpenOverlay\Models\Twitch\Emote;

class ChatEmotesClient extends ApiClient
{
    const MAX_SET_ID = 25;

    /**
     * @param  string  $broadcasterId
     * @return Emote[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Redbeed\OpenOverlay\Exceptions\AppTokenMissing
     */
    public function get(string $broadcasterId): array
    {
        $json = $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'broadcaster_id' => $broadcasterId,
                ],
            ])
            ->request('GET', 'chat/emotes');

        if (empty($json['data'])) {
            return [];
        }

        return $this->parseEmoteList($json['data']);
    }

    /**
     * @param  string  $broadcasterId
     * @return Emote[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Redbeed\OpenOverlay\Exceptions\AppTokenMissing
     */
    public function global(): array
    {
        $json = $this
            ->addAppToken()
            ->request('GET', 'chat/emotes/global');

        if (empty($json['data'])) {
            return [];
        }

        return $this->parseEmoteList($json['data']);
    }

    /**
     * @param  int  $setId
     * @return array
     *
     * @throws TwitchEmoteSetIdException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Redbeed\OpenOverlay\Exceptions\AppTokenMissing
     */
    public function set(int $setId): array
    {
        if ($setId > ChatEmotesClient::MAX_SET_ID || $setId < 1) {
            throw new TwitchEmoteSetIdException('Set Id minimum: 1 / maximum: '.ChatEmotesClient::MAX_SET_ID);
        }

        $json = $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'emote_set_id' => $setId,
                ],
            ])
            ->request('GET', 'chat/emotes/set');

        if (empty($json['data'])) {
            return [];
        }

        return $this->parseEmoteList($json['data']);
    }

    public function allSets(): array
    {
        $emotes = [];
        $bulkSize = 10;

        foreach (range(1, (ChatEmotesClient::MAX_SET_ID / $bulkSize)) as $bulk) {
            $to = min(($bulkSize * $bulk), ChatEmotesClient::MAX_SET_ID);
            $from = ($bulkSize * $bulk) - 9;

            $json = $this
                ->addAppToken()
                ->withOptions([
                    RequestOptions::QUERY => [
                        'emote_set_id' => range($from, $to),
                    ],
                ])
                ->request('GET', 'chat/emotes/set');

            if (empty($json['data'])) {
                continue;
            }

            $emotes += $json['data'];
        }

        return $this->parseEmoteList($emotes);
    }

    private function parseEmoteList(array $emoteArray): array
    {
        return collect($emoteArray)
            ->map(function ($emoteData) {
                return Emote::fromJson($emoteData);
            })
            ->toArray();
    }
}
