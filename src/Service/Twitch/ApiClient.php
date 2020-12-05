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

    /** @var array */
    protected $options = [];

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

    public static function http(): ApiClient
    {
        return new self();
    }

    public static function withAppToken(string $appToken): self
    {
        return (new self())->setOptions([
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$appToken,
            ],
        ]);
    }

    public function withOptions(array $options): self
    {
        $self = clone $this;
        $self->options = array_merge_recursive($this->options, $options);

        return $self;
    }

    public function setOptions(array $options): self
    {
        $self = clone $this;
        $self->options = $options;

        return $self;
    }

    public function request(string $method, string $url): array
    {
        $response = ApiClient::http()->request($method, $url, $this->options);
        $json = (string) $response->getBody();

        return json_decode($json, true);
    }
}
