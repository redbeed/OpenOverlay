<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class ApiClient
{
    protected $baseUrl = 'https://api.twitch.tv/helix/';

    /** @var Client */
    protected $httpClient;

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

    /**
     * @return ApiClient
     */
    public static function http()
    {
        return new static();
    }

    /**
     * @param  string  $appToken
     *
     * @return static
     */
    public static function withAppToken(string $appToken)
    {
        return (new static())->setOptions([
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$appToken,
            ],
        ]);
    }

    /**
     * @param  array  $options
     *
     * @return static
     */
    public function withOptions(array $options)
    {
        $self = clone $this;
        $self->options = array_merge_recursive($this->options, $options);

        return $self;
    }

    /**
     * @param  array  $options
     *
     * @return static
     */
    public function setOptions(array $options): self
    {
        $self = clone $this;
        $self->options = $options;

        return $self;
    }

    /**
     * @param  string  $method
     * @param  string  $url
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $url)
    {
        $response = $this->httpClient->request($method, $url, $this->options);
        $json = (string) $response->getBody();

        return json_decode($json, true);
    }
}
