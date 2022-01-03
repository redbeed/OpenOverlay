<?php

namespace Redbeed\OpenOverlay\Console\Commands\ChatBot;

use Illuminate\Support\Facades\Cache;
use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;
use Redbeed\OpenOverlay\Models\BotConnection;
use function Ratchet\Client\connect;

class StartCommand extends RuntimeCommand
{

    const RESTART_CACHE_KEY = 'redbeed:open-overlay:chat-bot:restart';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:chatbot:serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chat Bot worker (loop service)';

    /**
     *
     * Timestamp of the last restart
     *
     */
    private int $lastRestart;


    public function handle(): void
    {
        $this->configureRestartTimer();
        $this->configureChatbot();
    }

    private function configureRestartTimer()
    {
        $this->lastRestart = $this->getLastShutdown();

        $this->loop->addPeriodicTimer(10, function () {
            if ($this->lastRestart !== $this->getLastShutdown()) {
                $this->softShutdown();
            }
        });
    }

    private function configureChatbot()
    {
        /** @var BotConnection $bot */
        $bot = BotConnection::first();

        connect(ConnectionHandler::TWITCH_IRC_URL, [], [], $this->loop)
            ->then(function (WebSocket $conn) use ($bot) {
                $connectionHandler = ConnectionHandler::withPrivateMessageHandler($conn);
                $connectionHandler->auth($bot);

                foreach ($bot->users as $user) {
                    $twitchUsers = $user->connections()->where('service', 'twitch')->get();

                    $connectionHandler->initCustomCommands();

                    foreach ($twitchUsers as $twitchUser) {
                        $connectionHandler->joinChannel($twitchUser);
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

    public function getLastShutdown()
    {
        return Cache::get(self::RESTART_CACHE_KEY, 0);
    }
}
