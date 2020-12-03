<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use Redbeed\OpenOverlay\Exceptions\AppTokenMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookCallbackMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookSecretMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookTwitchSignatureMissing;

class EventSubClient
{
    /** @var array */
    protected $headers = [];

    public function __construct()
    {
        $appToken = config('openoverlay.webhook.twitch.app_token.token');

        if (empty($appToken)) {
            throw new AppTokenMissing('App Token is needed');
        }

        $this->headers([
            'Authorization' => 'Bearer '.$appToken,
        ]);
    }

    public static function verifySignature(
        string $messageSignature,
        string $messageId,
        string $messageTimestamp,
        string $requestBody
    ): bool {
        if (empty($messageId) || empty($messageSignature) || empty($messageTimestamp) || empty($requestBody)) {
            throw new WebhookTwitchSignatureMissing('Twitch Eventsub Header infomation missing');
        }

        $message = $messageId.$messageTimestamp.$requestBody;
        $hash = 'sha256='.hash_hmac('sha256', $message, config('openoverlay.webhook.twitch.secret'));

        return $hash === $messageSignature;
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

    public function subscribe(string $type, string $webhookCallback, array $condition = []): array
    {
        $secret = config('openoverlay.webhook.twitch.secret');

        if (empty($webhookCallback)) {
            throw new WebhookCallbackMissing('Webhook URL is required');
        }

        if (empty($secret)) {
            throw new WebhookSecretMissing('Secret is required');
        }

        $response = ApiClient::http()->post(
            'eventsub/subscriptions',
            [
                RequestOptions::JSON => [
                    'type' => $type,
                    'version' => '1',
                    'condition' => $condition,
                    'transport' => [
                        'method' => 'webhook',
                        'callback' => $webhookCallback.'?'.time(),
                        'secret' => config('openoverlay.webhook.twitch.secret'),
                    ],
                ],
                RequestOptions::HEADERS => $this->buildHeader(),
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }


    public function deleteSubscription(string $id) {
        $response = ApiClient::http()->delete(
            'eventsub/subscriptions',
            [
                RequestOptions::HEADERS => $this->buildHeader(),
                RequestOptions::QUERY => [
                    'id' => $id,
                ]
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }

    public function listSubscriptions() {
        $response = ApiClient::http()->get(
            'eventsub/subscriptions',
            [
                RequestOptions::HEADERS => $this->buildHeader(),
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }
}
