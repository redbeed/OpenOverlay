<?php

namespace Redbeed\OpenOverlay\Events\Twitch;

use Redbeed\OpenOverlay\Models\User\Connection;

class RefresherEvent
{
    /** @var Connection */
    public $twitchConnection;

    public function __construct(Connection $connection)
    {
        $this->twitchConnection = $connection;
    }
}
