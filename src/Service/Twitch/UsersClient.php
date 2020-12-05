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
}
