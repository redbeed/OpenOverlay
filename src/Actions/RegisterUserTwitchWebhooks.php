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
        $this->apiClient = new EventSubClient();
    }

    public static function registerAll(Connection $connection)
    {
        $webhooks = config('openoverlay.webhook.twitch.subscribe');
        $handler = new self($connection);

        foreach ($webhooks as $webhookName) {
            $handler->register($webhookName);
        }
    }

    public function register(string $type): bool
    {
        $jsonResponse = $this->apiClient->subscribe(
            $type,
            route('open_overlay.connection.webhook'),
            ['broadcaster_user_id' => (string) $this->connection->service_user_id]
        );

        $subscribeStatus = Arr::first($jsonResponse['data']);

        if ($subscribeStatus['status'] === 'webhook_callback_verification_pending') {
            return true;
        }

        dd($subscribeStatus);

        return false;
    }
}
