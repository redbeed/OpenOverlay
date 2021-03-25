<?php

namespace Redbeed\OpenOverlay\Console\Commands;


use Illuminate\Console\Command;
use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;
use Redbeed\OpenOverlay\Models\BotConnection;
use function Ratchet\Client\connect;

class ChatBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:chatbot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat Bot worker (loop service)';


    public function handle(): void
    {
        /** @var BotConnection $bot */
        $bot = BotConnection::first();

        connect(ConnectionHandler::TWITCH_IRC_URL)->then(function (WebSocket $conn) use ($bot) {
            $connectionHandler = new ConnectionHandler($conn);

            $connectionHandler->auth($bot);

            foreach ($bot->users as $user) {
                $twitchUsers = $user->connections()->where('service', 'twitch')->get();

                $connectionHandler->initCustomCommands();

                foreach ($twitchUsers as $twitchUser) {
                    $connectionHandler->joinChannel($twitchUser->service_username);
                    $connectionHandler->sendChatMessage($twitchUser->service_username, 'Hello');
                }
            }


            $conn->on('close', function ($code = null, $reason = null) {
                echo "Connection closed ({$code} - {$reason})";
            });

        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });
    }
}
