<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class StreamsClient extends ApiClient
{
    public function byUserId(string $userId): array
    {
        return $this
            ->addAppToken()
            ->withOptions([
                RequestOptions::QUERY => [
                    'user_id' => $userId,
                ],
            ])
            ->request('GET', 'streams');
    }
}
