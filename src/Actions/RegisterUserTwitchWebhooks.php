<?php

namespace Redbeed\OpenOverlay\Actions;

use Illuminate\Support\Arr;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Service\Twitch\EventSubClient;

class RegisterUserTwitchWebhooks
{
    /** @var Connection */
    public $connection;

    /** @var EventSubClient */
    private $apiClient;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->apiClient = EventSubClient::http();
    }

    public static function registerAll(Connection $connection, bool $clearBeforeRegister = false)
    {
        $webhooks = config('openoverlay.webhook.twitch.subscribe');
        $handler = new self($connection);

        if ($clearBeforeRegister === true) {
            $handler->clearBroadcasterSubscriptions();
        }

        foreach ($webhooks as $webhookName) {
            $handler->register($webhookName);
        }
    }

    public function clearBroadcasterSubscriptions()
    {
        $this->apiClient->deleteSubByBroadcasterId((string)$this->connection->service_user_id);
    }

    public function register(string $type): bool
    {
        $version = '1';

        // @todo: remove if channel.raid is not in beta anymore
        if ($type === 'channel.raid') {
            $version = 'beta';
        }

        $jsonResponse = $this->apiClient->subscribe(
            $type,
            route('open_overlay.connection.webhook'),
            $this->registerCondition($type),
            $version
        );

        $subscribeStatus = Arr::first($jsonResponse['data']);

        if ($subscribeStatus['status'] === 'webhook_callback_verification_pending') {
            return true;
        }

        return false;
    }

    private function registerCondition($type): array
    {
        $broadcasterId = (string)$this->connection->service_user_id;

        if ($type === 'channel.raid') {
            return ['to_broadcaster_user_id' => $broadcasterId];
        }

        return ['broadcaster_user_id' => $broadcasterId];
    }
}
