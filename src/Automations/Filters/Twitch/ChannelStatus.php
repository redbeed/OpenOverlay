<?php

namespace Redbeed\OpenOverlay\Automations\Filters\Twitch;

use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Support\StreamerOnline;

class ChannelStatus extends \Redbeed\OpenOverlay\Automations\Filters\Filter
{
    public static string $name = 'Twitch Channel Status';

    public static string $description = 'Check if channel is online or offline';

    public const IS_ONLINE = 'online';

    public const IS_OFFLINE = 'offline';

    private Connection $connection;

    private ?string $onlineStatus = null;

    public function __construct(Connection $userConnection)
    {
        $this->connection = $userConnection;
    }

    private function setOnlineStatus(string $status): self
    {
        $this->onlineStatus = $status;

        return $this;
    }

    public function isOnline(): self
    {
        return $this->setOnlineStatus(self::IS_ONLINE);
    }

    public function isOffline(): self
    {
        return $this->setOnlineStatus(self::IS_OFFLINE);
    }

    protected function validateOnlineStatus(): bool
    {
        if ($this->onlineStatus === null) {
            return true;
        }

        $currentStatus = StreamerOnline::isOnline($this->connection->service_user_id) ? self::IS_ONLINE : self::IS_OFFLINE;

        return $currentStatus === $this->onlineStatus;
    }

    public function validate(): bool
    {
        if (! $this->validateOnlineStatus()) {
            return false;
        }

        return true;
    }

    public function settings(): array
    {
        return [
            'onlineStatus' => $this->onlineStatus,
        ];
    }
}
