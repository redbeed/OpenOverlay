<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;

class AuthClient extends ApiClient
{
    public function refreshToken(string $refreshToken): array
    {
        $clientSecret = config('services.twitch.client_secret');
        $clientId = config('services.twitch.client_id');

        return $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
            ])
            ->request('POST', 'https://id.twitch.tv/oauth2/token');
    }
}
