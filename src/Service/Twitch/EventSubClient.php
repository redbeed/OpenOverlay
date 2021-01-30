<?php

namespace Redbeed\OpenOverlay\Service\Twitch;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Redbeed\OpenOverlay\Exceptions\AppTokenMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookCallbackMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookSecretMissing;
use Redbeed\OpenOverlay\Exceptions\WebhookTwitchSignatureMissing;
use Redbeed\OpenOverlay\Models\Twitch\EventSubscription;

class EventSubClient extends ApiClient
{
    public const BASE_URL = 'eventsub/subscriptions';

    /**
     * @return EventSubClient
     * @throws AppTokenMissing
     */
    public static function http()
    {
        $appToken = config('openoverlay.webhook.twitch.app_token.token');

        if (empty($appToken)) {
            throw new AppTokenMissing('App Token is needed');
        }

        return (new self())->setOptions([
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $appToken,
            ],
        ]);
    }

    public static function verifySignature(
        string $messageSignature,
        string $messageId,
        string $messageTimestamp,
        string $requestBody
    ): bool
    {
        if (empty($messageId) || empty($messageSignature) || empty($messageTimestamp) || empty($requestBody)) {
            throw new WebhookTwitchSignatureMissing('Twitch Eventsub Header infomation missing');
        }

        $message = $messageId . $messageTimestamp . $requestBody;
        $hash = 'sha256=' . hash_hmac('sha256', $message, config('openoverlay.webhook.twitch.secret'));

        return $hash === $messageSignature;
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

        return $this
            ->withOptions([
                RequestOptions::JSON => [
                    'type' => $type,
                    'version' => '1',
                    'condition' => $condition,
                    'transport' => [
                        'method' => 'webhook',
                        'callback' => $webhookCallback . '?' . time(),
                        'secret' => $secret,
                    ],
                ],
            ])
            ->request('POST', self::BASE_URL);
    }

    public function deleteSubByBroadcasterId(string $broadcasterUserId)
    {
        $subscriptions = $this
            ->subscriptions()
            ->filter(function ($subscription) use ($broadcasterUserId) {
            /** @var EventSubscription $subscription */
            if (empty($subscription->condition) && empty($subscription->condition['broadcaster_user_id'])) {
                return false;
            }

            if ($subscription->condition['broadcaster_user_id'] !== $broadcasterUserId) {
                return false;
            }

            return true;
        });

        foreach ($subscriptions as $subscription) {
            /** @var EventSubscription $subscription */
            $this->deleteSubscription($subscription->id);
        }

        $this->subscriptions();
    }

    public function deleteSubscription(string $id)
    {
        return $this
            ->withOptions([
                RequestOptions::QUERY => [
                    'id' => $id,
                ],
            ])
            ->request('DELETE', self::BASE_URL);
    }

    public function subscriptions(): Collection
    {
        $subData = $this->request('GET', self::BASE_URL);

        return collect($subData['data'])->map(function ($twitchData) {
            return EventSubscription::createFromTwitch($twitchData);
        });
    }
}
