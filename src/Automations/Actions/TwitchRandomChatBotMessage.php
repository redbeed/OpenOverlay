<?php

namespace Redbeed\OpenOverlay\Automations\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Redbeed\OpenOverlay\Console\Commands\ChatBot\SendMessageCommand;
use Redbeed\OpenOverlay\Models\User\Connection;

class TwitchRandomChatBotMessage
{
    use UseVariables;
    use UseTwitchChatMessage;

    public static string $name = 'Send one of the random chat messages';

    public static string $description = 'Send one of the random chat messages via bot';

    private Connection $connection;

    /** @var string[] */
    private array $messages;

    public function __construct(array $messages)
    {
        $this->messages = $messages;
    }

    public function handle()
    {
        Artisan::queue(SendMessageCommand::class, [
            'userId' => $this->getUser()->id,
            '--botId' => $this->getBot()->id,
            'message' => $this->replaceInString(Arr::random($this->messages)),
        ]);
    }
}
