<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class SubscriptionsClient extends ApiClient
{
    public function list(string $twitchUserId): array
    {
        return $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'broadcaster_id' => $twitchUserId,
                ],
            ])
            ->request('GET', 'subscriptions');
    }

    public function all(string $twitchUserId): array
    {
        $firstResponse = $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'first' => 100,
                ],
            ])->list($twitchUserId);

        $subscribers = $firstResponse['data'];
        $paginationCursor = $firstResponse['pagination']['cursor'] ?? null;

        while ($paginationCursor !== null) {
            $response = $this
                ->withOptions([
                    RequestOptions::QUERY => [
                        'first' => 100,
                        'after' => $paginationCursor,
                    ],
                ])
                ->list($twitchUserId);

            $paginationCursor = $response['pagination']['cursor'] ?? null;
            $subscribers = array_merge($subscribers, $response['data'] ?? []); // @todo: replace array_merge because its slow
        }

        $firstResponse['data'] = $subscribers;
        $firstResponse['pagination'] = [];

        return $firstResponse;
    }
}
