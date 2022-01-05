<?php

namespace Redbeed\OpenOverlay\Console\Commands\ChatBot;

use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Twitch\ConnectionHandler;
use Redbeed\OpenOverlay\Models\BotConnection;
use Redbeed\OpenOverlay\Models\User\Connection;
use Redbeed\OpenOverlay\Models\User\UserOpenOverlay;
use Redbeed\OpenOverlay\OpenOverlay;
use function Ratchet\Client\connect;

class SendMessageCommand extends RuntimeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overlay:chatbot:message {userId} {message} {--botId=} {--max-runtime=60}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Chatbot messages';

    public function handle(): void
    {
        $this->configureMaxRuntime();

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

        connect(ConnectionHandler::TWITCH_IRC_URL, [], [], $this->loop)
            ->then(function (WebSocket $conn) use ($bot, $user, $message) {
                $connectionHandler = new ConnectionHandler($conn);
                $connectionHandler->auth($bot);

                /** @var Connection[] $twitchUsers */
                $twitchUsers = $user->connections()->where('service', 'twitch')->get();

                foreach ($twitchUsers as $twitchUser) {

                    $connectionHandler->joinChannel($twitchUser);
                    $connectionHandler->sendChatMessage($twitchUser->service_username, $message);

                    // close connection after message send (5s delay)
                    $connectionHandler->addJoinedCallBack($twitchUser, function () {

                        // add delay to make sure the messages send correctly
                        $this->loop->addTimer(5, function () {
                            $this->softShutdown();
                        });
                    });
                }

            }, function ($e) {
                echo "Could not connect: {$e->getMessage()}\n";
            });
    }

    private function configureMaxRuntime()
    {
        $maxRuntime = $this->option('max-runtime');

        $this->loop->addPeriodicTimer($maxRuntime, function () {
            $this->softShutdown();
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
