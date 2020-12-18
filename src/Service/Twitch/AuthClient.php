<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class AuthClient extends ApiClient
{
    public function refreshToken(string $refreshToken): array
    {
        return $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
            ])
            ->request('POST', 'https://id.twitch.tv/oauth2/token');
    }
}
