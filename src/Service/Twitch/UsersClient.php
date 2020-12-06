<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class UsersClient extends ApiClient
{
    public function followers(string $twitchUserId): array
    {
        return $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'to_id' => $twitchUserId,
                ],
            ])
            ->request('GET', 'users/follows');
    }

    public function allFollowers(string $twitchUserId): array
    {
        $firstResponse = $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'first' => 100,
                ],
            ])->followers($twitchUserId);

        $totalFollowers = $firstResponse['total'];
        $followers = $firstResponse['data'];
        $paginationCursor = $firstResponse['pagination']['cursor'] ?? null;

        while ($totalFollowers > count($followers) || $paginationCursor !== null) {
            $response = $this
                ->withOptions([
                    RequestOptions::QUERY => [
                        'first' => 100,
                        'after' => $paginationCursor,
                    ],
                ])
                ->followers($twitchUserId);

            $paginationCursor = $response['pagination']['cursor'] ?? null;
            $followers = array_merge($followers, $response['data'] ?? []); // @todo: replace array_merge because its slow
        }

        $firstResponse['data'] = $followers;
        $firstResponse['pagination'] = [];

        return $firstResponse;
    }
}
