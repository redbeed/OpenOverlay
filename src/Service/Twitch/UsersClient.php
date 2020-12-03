<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Redbeed\OpenOverlay\Exceptions\WebhookCallbackMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookSecretMissing;

class UsersClient
{
    /** @var array */
    protected $headers = [];

    public static function withAppToken(string $appToken): self
    {
        $self = new UsersClient();

        $self->headers([
            'Authorization' => 'Bearer '.$appToken,
        ]);

        return $self;
    }

    public function headers($headers = []): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    protected function buildHeader($headers = []): array
    {
        return array_merge($this->headers, $headers);
    }

    public function get(): array
    {


        $response = ApiClient::http()->post(
            'users',
            []
        );

        return json_decode((string) $response->getBody(), true);
    }
}
