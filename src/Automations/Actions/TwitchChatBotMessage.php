<?php

namespace Redbeed\OpenOverlay\Automations\Actions;

use Illuminate\Support\Facades\Artisan;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\SendMessageCommand;
use Redbeed\OpenOverlay\Models\User\Connection;

class TwitchChatBotMessage
{
    use UsesVariables;

    private Connection $connection;
    private string $message;

    public function __construct(Connection $connection, string $message)
    {
        $this->connection = $connection;
        $this->message = $message;
    }

    public function handle()
    {
        Artisan::call(SendMessageCommand::class, [
            'userId'  => $this->connection->user->id,
            'message' => $this->replaceInString($this->message),
        ]);
    }
}
