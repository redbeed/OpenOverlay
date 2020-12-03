<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiClient
{
    protected $baseUrl = 'https://api.twitch.tv/helix/';

    /** @var Client */
    protected $httpClient;

    /** @var ApiClient */
    private static $shared;

    public function __construct()
    {
        $authCode = config('services.twitch.client_secret');
        $clientId = config('services.twitch.client_id');

        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            RequestOptions::HEADERS => [
                'Client-ID' => $clientId,
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$authCode,
            ],
        ]);
    }

    public static function http(): Client
    {
        if (self::$shared === null) {
            self::$shared = new self();
        }

        return self::$shared->httpClient;
    }
}
