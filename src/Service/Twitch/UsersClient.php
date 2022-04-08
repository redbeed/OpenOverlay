<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

class UsersClient extends ApiClient
{
    public function byId(string $id): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'id' => $id,
                ],
            ])
            ->request('GET', 'users');
    }

    public function byUsername(string $username): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'login' => $username,
                ],
            ])
            ->request('GET', 'users');
    }

    public function followers(string $twitchUserId): array
    {
        return $this
            ->addAppToken()
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
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'first' => 100,
                ],
            ])
            ->followers($twitchUserId);

        $totalFollowers = $firstResponse['total'];
        $followers = $firstResponse['data'];
        $paginationCursor = $firstResponse['pagination']['cursor'] ?? null;

        while ($totalFollowers > count($followers) || $paginationCursor !== null) {
            $response = $this
                ->addAppToken()
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
