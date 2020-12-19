<?php

namespace Redbeed\OpenOverlay\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Redbeed\OpenOverlay\Models\BotConnection;

class TwitchBotTokenExpires
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var BotConnection */
    public $botModel;

    public function __construct(BotConnection $botModel)
    {
        $this->botModel = $botModel;
    }
}
