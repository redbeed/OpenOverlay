<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Redbeed\OpenOverlay\Exceptions\AppTokenMissing;

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
                'Authorization' => 'Bearer ' . $authCode,
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
     * @return static
     * @throws AppTokenMissing
     */
    public function addAppToken()
    {
        $appToken = config('openoverlay.webhook.twitch.app_token.token');

        if (empty($appToken)) {
            throw new AppTokenMissing('App Token is needed');
        }

        return $this->withOptions([
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $appToken,
            ],
        ]);
    }

    /**
     * @param string $appToken
     *
     * @return static
     */
    public function withAppToken(string $appToken)
    {
        return $this->setOptions([
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $appToken,
            ],
        ]);
    }

    /**
     * @param array $options
     *
     * @return static
     */
    public function withOptions(array $options)
    {
        $self = clone $this;
        $self->options = array_replace_recursive($this->options, $options);

        return $self;
    }

    /**
     * @param array $options
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
     * @param string $method
     * @param string $url
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $method, string $url)
    {
        $response = $this->httpClient->request($method, $url, $this->options);
        $json = (string)$response->getBody();

        return json_decode($json, true);
    }
}
