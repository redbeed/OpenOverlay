<?php

namespace Redbeed\OpenOverlay\Console\Commands;


use Illuminate\Console\Command;
use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;
use Redbeed\OpenOverlay\Events\TwitchBotTokenExpires;
use Redbeed\OpenOverlay\Models\BotConnection;
use Redbeed\OpenOverlay\Models\User\Connection;
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
    protected $description = 'Generate new secret for twitch safer communication';


    public function handle(): void
    {
        /** @var BotConnection $bot */
        $bot = BotConnection::first();

        connect('wss://irc-ws.chat.twitch.tv:443')->then(function (WebSocket $conn) use ($bot) {
            $connectionHandler = new ConnectionHandler($conn);

            $connectionHandler->auth($bot->service_token, $bot->bot_username);

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

            $conn->on('message', function ($message) use ($bot) {

                // check if auth failed
                if (strpos($message, ':tmi.twitch.tv NOTICE * :Login authentication failed') !== false) {
                    event(new TwitchBotTokenExpires($bot));

                    return;
                }

            });

        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });
    }
}
