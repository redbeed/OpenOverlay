<?php

namespace Redbeed\OpenOverlay\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;
use Redbeed\OpenOverlay\Models\BotConnection;
use Redbeed\OpenOverlay\Models\User\UserOpenOverlay;
use Redbeed\OpenOverlay\OpenOverlay;
use function Ratchet\Client\connect;

class ChatBotMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:chatbot:message {userId} {message} {--botId=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new secret for twitch safer communication';


    public function handle(): void
    {
        $user = $this->getUser();
        $message = $this->argument('message');

        if (trim($message) === null) {
            $this->error('Message not filled');
            return;
        }

        if ($user === null) {
            $this->error('User not found');
            return;
        }

        $bot = $this->getBot();

        if ($bot === null) {
            $this->error('Bot not found');
            return;
        }

        connect('wss://irc-ws.chat.twitch.tv:443')->then(function (WebSocket $conn) use ($bot, $user, $message) {
            $connectionHandler = new ConnectionHandler($conn);

            $connectionHandler->auth($bot->service_token, $bot->bot_username);
            $twitchUsers = $user->connections()->where('service', 'twitch')->get();

            foreach ($twitchUsers as $twitchUser) {
                $connectionHandler->joinChannel($twitchUser->service_username);
                $connectionHandler->sendChatMessage($twitchUser->service_username, $message);
            }

            $conn->close();

        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });
    }

    private function getUser()
    {
        $userId = $this->argument('userId');
        return (OpenOverlay::userModel())::find($userId);
    }

    private function getBot(): ?BotConnection
    {
        /** @var UserOpenOverlay $user */
        $user = $this->getUser();
        $botId = $this->option('botId');

        if ($botId === null) {
            return $user->bots()->where('service', 'twitch')->first();
        }

        return $user->bots()->where('id', $botId)->first();
    }
}
