<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class UsersClient extends ApiClient
{
    public function followers(string $userId): array
    {
        return $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'to_id' => $userId,
                ],
            ])
            ->request('GET', 'users/follows');
    }

    public function allFollowers(string $userId): array
    {
        $firstResponse = $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'first' => 1,
                ],
            ])->followers($userId);

        $totalFollowers = $firstResponse['total'];
        $followers = $firstResponse['data'];
        $paginationCursor = $firstResponse['pagination']['cursor'] ?? null;

        while ($totalFollowers > count($followers) || $paginationCursor !== null) {
            $response = $this
                ->withOptions([
                    RequestOptions::QUERY => [
                        'first' => 1,
                        'after' => $paginationCursor,
                    ],
                ])
                ->followers($userId);

            $paginationCursor = $response['pagination']['cursor'] ?? null;
            $followers = array_push($followers, ...$response['data'] ?? []);
        }

        $firstResponse['data'] = $followers;
        $firstResponse['pagination'] = [];

        return $firstResponse;
    }
}
