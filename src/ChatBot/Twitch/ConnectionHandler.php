<?php


namespace Redbeed\OpenOverlay\ChatBot\Twitch;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Ratchet\Client\WebSocket;
use Redbeed\OpenOverlay\ChatBot\Commands\BotCommand;
use Redbeed\OpenOverlay\ChatBot\Commands\SimpleBotCommands;
use Redbeed\OpenOverlay\Events\TwitchChatMessageReceived;

class ConnectionHandler
{

    /** @var WebSocket */
    private $connection;

    /** @var BotCommand[] */
    private $customCommands = [];


    public function __construct(WebSocket $connection)
    {
        $this->connection = $connection;

        $this->connection->on('message', function ($message) {
            $this->messageReceived($message);
        });
    }

    public function messageReceived(string $message): void
    {
        // get join message
        if (strpos($message, 'PING') !== false) {
            $this->pingReceived($message);

            return;
        }

        // if is chat message
        if (strpos($message, 'PRIVMSG') !== false) {
            $this->chatMessageReceived($message);

            return;
        }

        // get join message
        if (strpos($message, 'JOIN') !== false) {
            $this->joinMessageReceived($message);

            return;
        }

        echo "UNKOWN | " . $message . "\r\n\r\n";
    }

    public function pingReceived(string $message): void
    {
        $this->send('PONG :tmi.twitch.tv');
        echo "PING PONG done" . "\r\n";
    }

    public function joinMessageReceived(string $message): void
    {
        try {
            preg_match("/:(.*)\!.*#(.*)/", $message, $matches);

            echo "BOT (" . $matches[1] . ") joined " . $matches[2] . "\r\n";
        } catch (\Exception $exception) {
            echo "ORIGINAL: " . $message;
            echo $exception->getMessage() . ' ' . $exception->getLine();
        }
    }

    public function chatMessageReceived(string $message): void
    {
        $model = ChatMessage::parseIRCMessage($message);

        if ($model === null) {
            return;
        }

        echo $model->channel . ' | ' . $model->username . ': ' . $model->message . "\r\n";

        try {
            // Check commands
            foreach ($this->customCommands as $commandHandler) {
                $commandHandler->handle($model);
            }
        }catch (\Exception $exception) {
            echo $exception->getMessage()."\r\n";
            echo $exception->getFile()."\r\n";
            echo $exception->getLine()."\r\n";
        }

        echo $model->channel . ' | ' . $model->username . ': ' . $model->message . " HANDELD\r\n";

        try {
            event(new TwitchChatMessageReceived($model));
        } catch (\Exception $exception) {
            echo "  -> EVENT ERROR: " . $exception->getMessage();
        }
    }

    public function auth(string $authToken, string $appUserName)
    {
        $this->send('PASS oauth:' . $authToken);
        $this->send('NICK ' . strtolower($appUserName));
    }

    public function send(string $message): void
    {
        $this->connection->send($message);
    }

    public function joinChannel(string $channelName): void
    {
        $this->send('JOIN #' . strtolower($channelName));
    }

    public function sendChatMessage(string $channelName, string $message): void
    {
        echo 'PRIVMSG #' . strtolower($channelName) . ' :' . $message."\n\r";

        $this->send('PRIVMSG #' . strtolower($channelName) . ' :' . $message."\n\r");
    }

    public function initCustomCommands(): void
    {
        /** @var BotCommand[] $commandClasses */
        $commandClasses = config('openoverlay.bot.commands.advanced');

        // add simple command handler
        $commandClasses[] = SimpleBotCommands::class;

        var_dump($commandClasses);

        foreach ($commandClasses as $commandClass) {
            $this->customCommands[] = new $commandClass($this);
        }
    }

}
