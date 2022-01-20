<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class StreamsClient extends ApiClient
{
    public function byUserId(string $userId): array
    {
        return $this->byUserIds([$userId]);
    }

    public function byUsername(string $username): array
    {
        return $this->byUsernames([$username]);
    }

    public function byUserIds(array $userIds): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'user_id' => array_map(function ($userId) {
                        return intval($userId);
                    }, $userIds),
                ],
            ])
            ->request('GET', 'streams');
    }

    public function byUsernames(array $usernames): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'user_login' => array_map(function ($username) {
                        return strtolower($username);
                    }, $usernames),
                ],
            ])
            ->request('GET', 'streams');
    }
}
